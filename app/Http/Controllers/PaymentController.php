<?php
namespace Crater\Http\Controllers;

use Illuminate\Http\Request;
use Crater\CompanySetting;
use Crater\Currency;
use Crater\Company;
use Crater\Invoice;
use Crater\Payment;
use Crater\PaymentMethod;
use Carbon\Carbon;
use function MongoDB\BSON\toJSON;
use Crater\User;
use Crater\Http\Requests\PaymentRequest;
use Validator;
use Crater\Mail\PaymentPdf;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $payments = Payment::with(['user', 'invoice', 'paymentMethod'])
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'payments.payment_method_id')
            ->applyFilters($request->only([
                'search',
                'payment_number',
                'payment_method_id',
                'customer_id',
                'orderByField',
                'orderBy'
            ]))
            ->whereCompany($request->header('company'))
            ->select('payments.*', 'users.name', 'invoices.invoice_number', 'payment_methods.name as payment_mode')
            ->latest()
            ->paginate($limit);

        return response()->json([
            'payments' => $payments
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $payment_prefix = CompanySetting::getSetting('payment_prefix', $request->header('company'));
        $payment_num_auto_generate = CompanySetting::getSetting('payment_auto_generate', $request->header('company'));


        $nextPaymentNumberAttribute = null;
        $nextPaymentNumber = Payment::getNextPaymentNumber($payment_prefix);

        if ($payment_num_auto_generate == "YES") {
            $nextPaymentNumberAttribute = $nextPaymentNumber;
        }

        return response()->json([
            'customers' => User::where('role', 'customer')
                ->whereCompany($request->header('company'))
                ->get(),
            'paymentMethods' => PaymentMethod::whereCompany($request->header('company'))
                ->latest()
                ->get(),
            'nextPaymentNumberAttribute' => $nextPaymentNumberAttribute,
            'nextPaymentNumber' => $payment_prefix.'-'.$nextPaymentNumber,
            'payment_prefix' => $payment_prefix
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentRequest $request)
    {
        $payment_number = explode("-",$request->payment_number);
        $number_attributes['payment_number'] = $payment_number[0].'-'.sprintf('%06d', intval($payment_number[1]));

        Validator::make($number_attributes, [
            'payment_number' => 'required|unique:payments,payment_number'
        ])->validate();

        $payment_date = Carbon::createFromFormat('d/m/Y', $request->payment_date);

        if ($request->has('invoice_id') && $request->invoice_id != null) {
            $invoice = Invoice::find($request->invoice_id);
            if ($invoice && $invoice->due_amount == $request->amount) {
                $invoice->status = Invoice::STATUS_COMPLETED;
                $invoice->paid_status = Invoice::STATUS_PAID;
                $invoice->due_amount = 0;
            } elseif ($invoice && $invoice->due_amount != $request->amount) {
                $invoice->due_amount = (int)$invoice->due_amount - (int)$request->amount;
                if ($invoice->due_amount < 0) {
                    return response()->json([
                        'error' => 'invalid_amount'
                    ]);
                }
                $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
            }
            $invoice->save();
        }

        $payment = Payment::create([
            'payment_date' => $payment_date,
            'payment_number' => $number_attributes['payment_number'],
            'user_id' => $request->user_id,
            'company_id' => $request->header('company'),
            'invoice_id' => $request->invoice_id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'unique_hash' => str_random(60)
        ]);

        return response()->json([
            'payment' => $payment,
            'shareable_link' => url('/payments/pdf/'.$payment->unique_hash),
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment = Payment::with(['user', 'invoice', 'paymentMethod'])->find($id);

        return response()->json([
            'payment' => $payment,
            'shareable_link' => url('/payments/pdf/'.$payment->unique_hash)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $payment = Payment::with(['user', 'invoice', 'paymentMethod'])->find($id);

        $invoices = Invoice::where('paid_status', '<>', Invoice::STATUS_PAID)
            ->where('user_id', $payment->user_id)->where('due_amount', '>', 0)
            ->whereCompany($request->header('company'))
            ->get();

        return response()->json([
            'customers' => User::where('role', 'customer')
                ->whereCompany($request->header('company'))
                ->get(),
            'paymentMethods' => PaymentMethod::whereCompany($request->header('company'))
                ->latest()
                ->get(),
            'nextPaymentNumber' => $payment->getPaymentNumAttribute(),
            'payment_prefix' => $payment->getPaymentPrefixAttribute(),
            'shareable_link' => url('/payments/pdf/'.$payment->unique_hash),
            'payment' => $payment,
            'invoices' => $invoices
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentRequest $request, $id)
    {
        $payment_number = explode("-",$request->payment_number);
        $number_attributes['payment_number'] = $payment_number[0].'-'.sprintf('%06d', intval($payment_number[1]));

        Validator::make($number_attributes, [
            'payment_number' => 'required|unique:payments,payment_number'.','.$id
        ])->validate();

        $payment_date = Carbon::createFromFormat('d/m/Y', $request->payment_date);

        $payment = Payment::find($id);
        $oldAmount = $payment->amount;

        if ($request->has('invoice_id') && $request->invoice_id && ($oldAmount != $request->amount)) {
            $amount = (int)$request->amount - (int)$oldAmount;
            $invoice = Invoice::find($request->invoice_id);
            $invoice->due_amount = (int)$invoice->due_amount - (int)$amount;

            if ($invoice->due_amount < 0) {
                return response()->json([
                    'error' => 'invalid_amount'
                ]);
            }

            if ($invoice->due_amount == 0) {
                $invoice->status = Invoice::STATUS_COMPLETED;
                $invoice->paid_status = Invoice::STATUS_PAID;
            } else {
                $invoice->status = $invoice->getPreviousStatus();
                $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
            }

            $invoice->save();
        }

        $payment->payment_date = $payment_date;
        $payment->payment_number = $number_attributes['payment_number'];
        $payment->user_id = $request->user_id;
        $payment->invoice_id = $request->invoice_id;
        $payment->payment_method_id = $request->payment_method_id;
        $payment->amount = $request->amount;
        $payment->notes = $request->notes;
        $payment->save();

        return response()->json([
            'payment' => $payment,
            'shareable_link' => url('/payments/pdf/'.$payment->unique_hash),
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = Payment::find($id);

        if ($payment->invoice_id != null) {
            $invoice = Invoice::find($payment->invoice_id);
            $invoice->due_amount = ((int)$invoice->due_amount + (int)$payment->amount);

            if ($invoice->due_amount == $invoice->total) {
                $invoice->paid_status = Invoice::STATUS_UNPAID;
            } else {
                $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
            }

            $invoice->status = $invoice->getPreviousStatus();
            $invoice->save();
        }

        $payment->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function delete(Request $request)
    {
        foreach ($request->id as $id) {
            $payment = Payment::find($id);

            if ($payment->invoice_id != null) {
                $invoice = Invoice::find($payment->invoice_id);
                $invoice->due_amount = ((int)$invoice->due_amount + (int)$payment->amount);

                if ($invoice->due_amount == $invoice->total) {
                    $invoice->paid_status = Invoice::STATUS_UNPAID;
                } else {
                    $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
                }

                $invoice->status = $invoice->getPreviousStatus();
                $invoice->save();
            }

            $payment->delete();
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function sendPayment(Request $request)
    {
        $payment = Payment::findOrFail($request->id);

        $data['payment'] = $payment->toArray();
        $userId = $data['payment']['user_id'];
        $data['user'] = User::find($userId)->toArray();
        $data['company'] = Company::find($payment->company_id);
        $email = $data['user']['email'];

        if (!$email) {
            return response()->json([
                'error' => 'user_email_does_not_exist'
            ]);
        }

        \Mail::to($email)->send(new PaymentPdf($data));

        return response()->json([
            'success' => true
        ]);
    }
}
