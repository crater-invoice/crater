<?php

namespace Crater\Models;

use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Crater\Jobs\GeneratePaymentPdfJob;
use Crater\Mail\SendPaymentMail;
use Crater\Traits\GeneratesPdfTrait;
use Crater\Traits\HasCustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Vinkla\Hashids\Facades\Hashids;

class Payment extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use GeneratesPdfTrait;
    use HasCustomFieldsTrait;

    public const PAYMENT_MODE_CHECK = 'CHECK';
    public const PAYMENT_MODE_OTHER = 'OTHER';
    public const PAYMENT_MODE_CASH = 'CASH';
    public const PAYMENT_MODE_CREDIT_CARD = 'CREDIT_CARD';
    public const PAYMENT_MODE_BANK_TRANSFER = 'BANK_TRANSFER';

    protected $dates = ['created_at', 'updated_at'];

    protected $guarded = ['id'];

    protected $appends = [
        'formattedCreatedAt',
        'formattedPaymentDate',
        'paymentPdfUrl',
    ];

    protected static function booted()
    {
        static::created(function ($payment) {
            GeneratePaymentPdfJob::dispatch($payment);
        });

        static::updated(function ($payment) {
            GeneratePaymentPdfJob::dispatch($payment, true);
        });
    }

    public function setPaymentDateAttribute($value)
    {
        if ($value) {
            $this->attributes['payment_date'] = Carbon::createFromFormat('Y-m-d', $value);
        }
    }

    public function getPaymentPrefixAttribute()
    {
        $prefix = explode("-", $this->payment_number)[0];

        return $prefix;
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function getFormattedPaymentDateAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->payment_date)->format($dateFormat);
    }

    public function getPaymentPdfUrlAttribute()
    {
        return url('/payments/pdf/'.$this->unique_hash);
    }

    public function getPaymentNumAttribute()
    {
        $position = $this->strposX($this->payment_number, "-", 1) + 1;

        return substr($this->payment_number, $position);
    }

    public function emailLogs()
    {
        return $this->morphMany('App\Models\EmailLog', 'mailable');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo('Crater\Models\User', 'creator_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function send($data)
    {
        $data['payment'] = $this->toArray();
        $data['user'] = $this->user->toArray();
        $data['company'] = Company::find($this->company_id);
        $data['body'] = $this->getEmailBody($data['body']);
        $data['attach']['data'] = ($this->getEmailAttachmentSetting()) ? $this->getPDFData() : null;

        \Mail::to($data['to'])->send(new SendPaymentMail($data));

        return [
            'success' => true,
        ];
    }

    public static function createPayment($request)
    {
        $data = $request->validated();

        $data['company_id'] = $request->header('company');
        $data['creator_id'] = Auth::id();

        if ($request->has('invoice_id') && $request->invoice_id != null) {
            $invoice = Invoice::find($request->invoice_id);
            if ($invoice && $invoice->due_amount == $request->amount) {
                $invoice->status = Invoice::STATUS_COMPLETED;
                $invoice->paid_status = Invoice::STATUS_PAID;
                $invoice->due_amount = 0;
            } elseif ($invoice && $invoice->due_amount != $request->amount) {
                $invoice->due_amount = (int)$invoice->due_amount - (int)$request->amount;
                if ($invoice->due_amount < 0) {
                    return [
                        'error' => 'invalid_amount',
                    ];
                }
                $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
            }
            $invoice->save();
        }


        $payment = Payment::create($data);

        $payment->unique_hash = Hashids::connection(Payment::class)->encode($payment->id);

        $payment->save();

        $customFields = $request->customFields;

        if ($customFields) {
            $payment->addCustomFields($customFields);
        }

        $payment = Payment::with([
            'user',
            'invoice',
            'paymentMethod',
        ])->find($payment->id);

        return $payment;
    }

    public function updatePayment($request)
    {
        $oldAmount = $this->amount;

        if ($request->has('invoice_id') && $request->invoice_id && ($oldAmount != $request->amount)) {
            $amount = (int)$request->amount - (int)$oldAmount;
            $invoice = Invoice::find($request->invoice_id);
            $invoice->due_amount = (int)$invoice->due_amount - (int)$amount;

            if ($invoice->due_amount < 0) {
                return [
                    'error' => 'invalid_amount',
                ];
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

        $data = $request->all();

        $this->update($data);

        $customFields = $request->customFields;

        if ($customFields) {
            $this->updateCustomFields($customFields);
        }

        $payment = Payment::with([
            'user',
            'invoice',
            'paymentMethod',
        ])
            ->find($this->id);

        return $payment;
    }

    public static function deletePayments($ids)
    {
        foreach ($ids as $id) {
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

        return true;
    }

    private function strposX($haystack, $needle, $number)
    {
        if ($number == '1') {
            return strpos($haystack, $needle);
        } elseif ($number > '1') {
            return strpos(
                $haystack,
                $needle,
                $this->strposX($haystack, $needle, $number - 1) + strlen($needle)
            );
        } else {
            return error_log('Error: Value for parameter $number is out of range');
        }
    }

    public static function getNextPaymentNumber($value)
    {
        // Get the last created order
        $payment = Payment::where('payment_number', 'LIKE', $value.'-%')
            ->orderBy('payment_number', 'desc')
            ->first();

        // Get number length config
        $numberLength = CompanySetting::getSetting('payment_number_length', request()->header('company'));
        $numberLengthText = "%0{$numberLength}d";

        if (! $payment) {
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;
        } else {
            $number = explode("-", $payment->payment_number);
            $number = $number[1];
        }
        // If we have ORD000001 in the database then we only want the number
        // So the substr returns this 000001

        // Add the string in front and higher up the number.
        // the %05d part makes sure that there are always 6 numbers in the string.
        // so it adds the missing zero's when needed.

        return sprintf($numberLengthText, intval($number) + 1);
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->whereHas('user', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%')
                    ->orWhere('contact_name', 'LIKE', '%'.$term.'%')
                    ->orWhere('company_name', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopePaymentNumber($query, $paymentNumber)
    {
        return $query->where('payments.payment_number', 'LIKE', '%'.$paymentNumber.'%');
    }

    public function scopePaymentMethod($query, $paymentMethodId)
    {
        return $query->where('payments.payment_method_id', $paymentMethodId);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('payment_number')) {
            $query->paymentNumber($filters->get('payment_number'));
        }

        if ($filters->get('payment_id')) {
            $query->wherePayment($filters->get('payment_id'));
        }

        if ($filters->get('payment_method_id')) {
            $query->paymentMethod($filters->get('payment_method_id'));
        }

        if ($filters->get('customer_id')) {
            $query->whereCustomer($filters->get('customer_id'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'payment_number';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWherePayment($query, $payment_id)
    {
        $query->orWhere('id', $payment_id);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('payments.company_id', $company_id);
    }

    public function scopeWhereCustomer($query, $customer_id)
    {
        $query->where('payments.user_id', $customer_id);
    }

    public function getPDFData()
    {
        $company = Company::find($this->company_id);
        $locale = CompanySetting::getSetting('language', $company->id);

        \App::setLocale($locale);

        $logo = $company->logo_path;

        view()->share([
            'payment' => $this,
            'company_address' => $this->getCompanyAddress(),
            'billing_address' => $this->getCustomerBillingAddress(),
            'notes' => $this->getNotes(),
            'logo' => $logo ?? null,
        ]);

        return PDF::loadView('app.pdf.payment.payment');
    }

    public function getCompanyAddress()
    {
        if ($this->company && (! $this->company->address()->exists())) {
            return false;
        }

        $format = CompanySetting::getSetting('payment_company_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getCustomerBillingAddress()
    {
        if ($this->user && (! $this->user->billingAddress()->exists())) {
            return false;
        }

        $format = CompanySetting::getSetting('payment_from_customer_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getEmailAttachmentSetting()
    {
        $paymentAsAttachment = CompanySetting::getSetting('payment_email_attachment', $this->company_id);

        if ($paymentAsAttachment == 'NO') {
            return false;
        }

        return true;
    }

    public function getNotes()
    {
        return $this->getFormattedString($this->notes);
    }

    public function getEmailBody($body)
    {
        $values = array_merge($this->getFieldsArray(), $this->getExtraFields());

        $body = strtr($body, $values);

        return preg_replace('/{(.*?)}/', '', $body);
    }

    public function getExtraFields()
    {
        return [
            '{PAYMENT_DATE}' => $this->formattedPaymentDate,
            '{PAYMENT_MODE}' => $this->paymentMethod ? $this->paymentMethod->name : null,
            '{PAYMENT_NUMBER}' => $this->payment_number,
            '{PAYMENT_AMOUNT}' => $this->reference_number,
            '{PAYMENT_LINK}' => $this->paymentPdfUrl,
        ];
    }
}
