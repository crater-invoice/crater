<?php
namespace Crater\Http\Controllers;

use Illuminate\Http\Request;
use Crater\Estimate;
use Crater\EstimateItem;
use Crater\EstimateTemplate;
use Carbon\Carbon;
use Crater\Http\Requests\EstimatesRequest;
use Crater\Invoice;
use Crater\Currency;
use Crater\User;
use Crater\Item;
use Validator;
use Crater\CompanySetting;
use Crater\Company;
use Crater\Mail\EstimatePdf;
use Crater\TaxType;
use Crater\Tax;

class EstimatesController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $estimates = Estimate::with([
                'items',
                'user',
                'estimateTemplate',
                'taxes'
            ])
            ->join('users', 'users.id', '=', 'estimates.user_id')
            ->applyFilters($request->only([
                'status',
                'customer_id',
                'estimate_number',
                'from_date',
                'to_date',
                'search',
                'orderByField',
                'orderBy'
            ]))
            ->whereCompany($request->header('company'))
            ->select('estimates.*', 'users.name')
            ->latest()
            ->paginate($limit);

        $siteData = [
            'estimates' => $estimates,
            'estimateTotalCount' => Estimate::count()
        ];

        return response()->json($siteData);
    }

    public function create(Request $request)
    {
        $estimate_prefix = CompanySetting::getSetting('estimate_prefix', $request->header('company'));
        $estimate_num_auto_generate = CompanySetting::getSetting('estimate_auto_generate', $request->header('company'));

        $nextEstimateNumberAttribute = null;
        $nextEstimateNumber = Estimate::getNextEstimateNumber($estimate_prefix);

        if ($estimate_num_auto_generate == "YES") {
            $nextEstimateNumberAttribute = $nextEstimateNumber;
        }

        $tax_per_item = CompanySetting::getSetting('tax_per_item', $request->header('company'));
        $discount_per_item = CompanySetting::getSetting('discount_per_item', $request->header('company'));
        $customers = User::where('role', 'customer')->get();

        return response()->json([
            'customers' => $customers,
            'nextEstimateNumberAttribute' => $nextEstimateNumberAttribute,
            'nextEstimateNumber' => $estimate_prefix.'-'.$nextEstimateNumber,
            'taxes' => Tax::whereCompany($request->header('company'))->latest()->get(),
            'items' => Item::whereCompany($request->header('company'))->get(),
            'tax_per_item' => $tax_per_item,
            'discount_per_item' => $discount_per_item,
            'estimateTemplates' => EstimateTemplate::all(),
            'shareable_link' => '',
            'estimate_prefix' => $estimate_prefix
        ]);
    }

    public function store(EstimatesRequest $request)
    {
        $estimate_number = explode("-",$request->estimate_number);
        $number_attributes['estimate_number'] = $estimate_number[0].'-'.sprintf('%06d', intval($estimate_number[1]));

        Validator::make($number_attributes, [
            'estimate_number' => 'required|unique:estimates,estimate_number'
        ])->validate();

        $estimate_date = Carbon::createFromFormat('d/m/Y', $request->estimate_date);
        $expiry_date = Carbon::createFromFormat('d/m/Y', $request->expiry_date);
        $status = Estimate::STATUS_DRAFT;
        $tax_per_item = CompanySetting::getSetting(
            'tax_per_item',
            $request->header('company')
        ) ? CompanySetting::getSetting(
            'tax_per_item',
            $request->header('company')
        ) : 'NO';

        if ($request->has('estimateSend')) {
            $status = Estimate::STATUS_SENT;
        }

        $discount_per_item = CompanySetting::getSetting(
            'discount_per_item',
            $request->header('company')
        ) ? CompanySetting::getSetting(
            'discount_per_item',
            $request->header('company')
        ) : 'NO';

        $estimate = Estimate::create([
            'estimate_date' => $estimate_date,
            'expiry_date' => $expiry_date,
            'estimate_number' => $number_attributes['estimate_number'],
            'reference_number' => $request->reference_number,
            'user_id' => $request->user_id,
            'company_id' => $request->header('company'),
            'estimate_template_id' => $request->estimate_template_id,
            'status' => $status,
            'discount' => $request->discount,
            'discount_type' => $request->discount_type,
            'discount_val' => $request->discount_val,
            'sub_total' => $request->sub_total,
            'total' => $request->total,
            'tax_per_item' => $tax_per_item,
            'discount_per_item' => $discount_per_item,
            'tax' => $request->tax,
            'notes' => $request->notes,
            'unique_hash' => str_random(60)
        ]);

        $estimateItems = $request->items;

        foreach ($estimateItems as $estimateItem) {
            $estimateItem['company_id'] = $request->header('company');
            $item = $estimate->items()->create($estimateItem);

            if (array_key_exists('taxes', $estimateItem) && $estimateItem['taxes']) {
                foreach ($estimateItem['taxes'] as $tax) {
                    if (gettype($tax['amount']) !== "NULL") {
                        $tax['company_id'] = $request->header('company');
                        $item->taxes()->create($tax);
                    }
                }
            }
        }

        if ($request->has('taxes')) {
            foreach ($request->taxes as $tax) {
                if (gettype($tax['amount']) !== "NULL") {
                    $tax['company_id'] = $request->header('company');
                    $estimate->taxes()->create($tax);
                }
            }
        }

        if ($request->has('estimateSend')) {
            $data['estimate'] = $estimate->toArray();
            $userId = $data['estimate']['user_id'];
            $data['user'] = User::find($userId)->toArray();
            $data['company'] = Company::find($estimate->company_id);
            $email = $data['user']['email'];

            if (!$email) {
                return response()->json([
                    'error' => 'user_email_does_not_exist'
                ]);
            }

            \Mail::to($email)->send(new EstimatePdf($data));
        }

        $estimate = Estimate::with([
            'items',
            'user',
            'estimateTemplate',
            'taxes'
        ])->find($estimate->id);

        return response()->json([
            'estimate' => $estimate,
            'url' => url('/estimates/pdf/'.$estimate->unique_hash),
        ]);
    }

    public function show(Request $request, $id)
    {
        $estimate = Estimate::with([
            'items',
            'items.taxes',
            'user',
            'estimateTemplate',
            'taxes',
            'taxes.taxType'
        ])->find($id);

        $siteData = [
            'estimate' => $estimate,
            'shareable_link' => url('/estimates/pdf/'.$estimate->unique_hash)
        ];

        return response()->json($siteData);
    }

    public function edit(Request $request,$id)
    {
        $estimate = Estimate::with([
            'items',
            'items.taxes',
            'user',
            'estimateTemplate',
            'taxes',
            'taxes.taxType'
        ])->find($id);
        $customers = User::where('role', 'customer')->get();

        return response()->json( [
            'customers' => $customers,
            'nextEstimateNumber' => $estimate->getEstimateNumAttribute(),
            'taxes' => Tax::latest()->whereCompany($request->header('company'))->get(),
            'estimate' => $estimate,
            'items' => Item::whereCompany($request->header('company'))->latest()->get(),
            'estimateTemplates' => EstimateTemplate::all(),
            'tax_per_item' => $estimate->tax_per_item,
            'discount_per_item' => $estimate->discount_per_item,
            'shareable_link' => url('/estimates/pdf/'.$estimate->unique_hash),
            'estimate_prefix' => $estimate->getEstimatePrefixAttribute()
        ]);
    }

    public function update(EstimatesRequest $request, $id)
    {
        $estimate_number = explode("-",$request->estimate_number);
        $number_attributes['estimate_number'] = $estimate_number[0].'-'.sprintf('%06d', intval($estimate_number[1]));
        Validator::make($number_attributes, [
            'estimate_number' => 'required|unique:estimates,estimate_number'.','.$id
        ])->validate();

        $estimate_date = Carbon::createFromFormat('d/m/Y', $request->estimate_date);
        $expiry_date = Carbon::createFromFormat('d/m/Y', $request->expiry_date);

        $estimate = Estimate::find($id);
        $estimate->estimate_date = $estimate_date;
        $estimate->expiry_date = $expiry_date;
        $estimate->estimate_number = $number_attributes['estimate_number'];
        $estimate->reference_number = $request->reference_number;
        $estimate->user_id = $request->user_id;
        $estimate->estimate_template_id = $request->estimate_template_id;
        $estimate->discount = $request->discount;
        $estimate->discount_type = $request->discount_type;
        $estimate->discount_val = $request->discount_val;
        $estimate->sub_total = $request->sub_total;
        $estimate->total = $request->total;
        $estimate->tax = $request->tax;
        $estimate->notes = $request->notes;
        $estimate->save();

        $oldItems = $estimate->items->toArray();
        $oldTaxes = $estimate->taxes->toArray();
        $estimateItems = $request->items;

        foreach ($oldItems as $oldItem) {
            EstimateItem::destroy($oldItem['id']);
        }

        foreach ($oldTaxes as $oldTax) {
            Tax::destroy($oldTax['id']);
        }

        foreach ($estimateItems as $estimateItem) {
            $estimateItem['company_id'] = $request->header('company');
            $item = $estimate->items()->create($estimateItem);

            if (array_key_exists('taxes', $estimateItem) && $estimateItem['taxes']) {
                foreach ($estimateItem['taxes'] as $tax) {
                    if (gettype($tax['amount']) !== "NULL") {
                        $tax['company_id'] = $request->header('company');
                        $item->taxes()->create($tax);
                    }
                }
            }
        }

        if ($request->has('taxes')) {
            foreach ($request->taxes as $tax) {
                if (gettype($tax['amount']) !== "NULL") {
                    $tax['company_id'] = $request->header('company');
                    $estimate->taxes()->create($tax);
                }
            }
        }

        $estimate = Estimate::with([
            'items',
            'user',
            'estimateTemplate',
            'taxes'
        ])->find($estimate->id);

        return response()->json([
            'estimate' => $estimate,
            'url' => url('/estimates/pdf/'.$estimate->unique_hash),
        ]);
    }

    public function destroy($id)
    {
        Estimate::deleteEstimate($id);

        return response()->json([
            'success' => true
        ]);
    }

    public function sendEstimate(Request $request)
    {
        $estimate = Estimate::findOrFail($request->id);

        $data['estimate'] = $estimate->toArray();
        $userId = $data['estimate']['user_id'];
        $data['user'] = User::find($userId)->toArray();
        $data['company'] = Company::find($estimate->company_id);

        $email = $data['user']['email'];

        if (!$email) {
            return response()->json([
                'error' => 'user_email_does_not_exist'
            ]);
        }

        \Mail::to($email)->send(new EstimatePdf($data));

        if ($estimate->status == Estimate::STATUS_DRAFT) {
            $estimate->status = Estimate::STATUS_SENT;
            $estimate->save();
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function markEstimateAccepted(Request $request)
    {
        $estimate = Estimate::find($request->id);
        $estimate->status = Estimate::STATUS_ACCEPTED;
        $estimate->save();

        return response()->json([
            'success' => true
        ]);
    }

    public function markEstimateRejected(Request $request)
    {
        $estimate = Estimate::find($request->id);
        $estimate->status = Estimate::STATUS_REJECTED;
        $estimate->save();

        return response()->json([
            'success' => true
        ]);
    }

    public function markEstimateSent(Request $request)
    {
        $estimate = Estimate::find($request->id);
        $estimate->status = Estimate::STATUS_SENT;
        $estimate->save();

        return response()->json([
            'success' => true
        ]);
    }

    public function estimateToInvoice(Request $request, $id)
    {
        $estimate = Estimate::with(['items', 'items.taxes', 'user', 'estimateTemplate', 'taxes'])->find($id);
        $invoice_date = Carbon::parse($estimate->estimate_date);
        $invoice_prefix = CompanySetting::getSetting(
            'invoice_prefix',
            $request->header('company')
        );
        $due_date = Carbon::parse($estimate->estimate_date)->addDays(7);
        $tax_per_item = CompanySetting::getSetting(
                'tax_per_item',
                $request->header('company')
            ) ? CompanySetting::getSetting(
                'tax_per_item',
                $request->header('company')
            ) : 'NO';
        $discount_per_item = CompanySetting::getSetting(
                'discount_per_item',
                $request->header('company')
            ) ? CompanySetting::getSetting(
                'discount_per_item',
                $request->header('company')
            ) : 'NO';

        $invoice = Invoice::create([
            'invoice_date' => $invoice_date,
            'due_date' => $due_date,
            'invoice_number' => $invoice_prefix."-".Invoice::getNextInvoiceNumber($invoice_prefix),
            'reference_number' => $estimate->reference_number,
            'user_id' => $estimate->user_id,
            'company_id' => $request->header('company'),
            'invoice_template_id' => 1,
            'status' => Invoice::STATUS_DRAFT,
            'paid_status' => Invoice::STATUS_UNPAID,
            'sub_total' => $estimate->sub_total,
            'discount' => $estimate->discount,
            'discount_type' => $estimate->discount_type,
            'discount_val' => $estimate->discount_val,
            'total' => $estimate->total,
            'due_amount' => $estimate->total,
            'tax_per_item' => $tax_per_item,
            'discount_per_item' => $discount_per_item,
            'tax' => $estimate->tax,
            'notes' => $estimate->notes,
            'unique_hash' => str_random(60)
        ]);

        $invoiceItems = $estimate->items->toArray();

        foreach ($invoiceItems as $invoiceItem) {
            $invoiceItem['company_id'] = $request->header('company');
            $invoiceItem['name'] = $invoiceItem['name'];
            $item = $invoice->items()->create($invoiceItem);

            if (array_key_exists('taxes', $invoiceItem) && $invoiceItem['taxes']) {
                foreach ($invoiceItem['taxes'] as $tax) {
                    $tax['company_id'] = $request->header('company');

                    if ($tax['amount']) {
                        $item->taxes()->create($tax);
                    }
                }
            }
        }

        if ($estimate->taxes) {
            foreach ($estimate->taxes->toArray() as $tax) {
                $tax['company_id'] = $request->header('company');
                $invoice->taxes()->create($tax);
            }
        }

        $invoice = Invoice::with([
            'items',
            'user',
            'invoiceTemplate',
            'taxes'
        ])->find($invoice->id);

        return response()->json([
            'invoice' => $invoice
        ]);
    }

    public function delete(Request $request)
    {
        foreach ($request->id as $id) {
            Estimate::deleteEstimate($id);
        }

        return response()->json([
            'success' => true
        ]);
    }
}
