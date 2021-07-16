<?php

namespace Crater\Models;

use App;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Crater\Mail\SendInvoiceMail;
use Crater\Traits\GeneratesPdfTrait;
use Crater\Traits\HasCustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Vinkla\Hashids\Facades\Hashids;

class Invoice extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use GeneratesPdfTrait;
    use HasCustomFieldsTrait;

    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_SENT = 'SENT';
    public const STATUS_VIEWED = 'VIEWED';
    public const STATUS_OVERDUE = 'OVERDUE';
    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_DUE = 'DUE';
    public const STATUS_UNPAID = 'UNPAID';
    public const STATUS_PARTIALLY_PAID = 'PARTIALLY_PAID';
    public const STATUS_PAID = 'PAID';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'total' => 'integer',
        'tax' => 'integer',
        'sub_total' => 'integer',
        'discount' => 'float',
        'discount_val' => 'integer',
    ];

    protected $guarded = [
        'id',
    ];

    protected $appends = [
        'formattedCreatedAt',
        'formattedInvoiceDate',
        'formattedDueDate',
        'invoicePdfUrl',
    ];

    public function setInvoiceDateAttribute($value)
    {
        if ($value) {
            $this->attributes['invoice_date'] = Carbon::createFromFormat('Y-m-d', $value);
        }
    }

    public function setDueDateAttribute($value)
    {
        if ($value) {
            $this->attributes['due_date'] = Carbon::createFromFormat('Y-m-d', $value);
        }
    }

    public static function getNextInvoiceNumber($value)
    {
        // Get the last created order
        $lastOrder = Invoice::where('invoice_number', 'LIKE', $value.'-%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        // Get number length config
        $numberLength = CompanySetting::getSetting('invoice_number_length', request()->header('company'));
        $numberLengthText = "%0{$numberLength}d";

        if (! $lastOrder) {
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;
        } else {
            $number = explode("-", $lastOrder->invoice_number);
            $number = $number[1];
        }
        // If we have ORD000001 in the database then we only want the number
        // So the substr returns this 000001

        // Add the string in front and higher up the number.
        // the %06d part makes sure that there are always 6 numbers in the string.
        // so it adds the missing zero's when needed.

        return sprintf($numberLengthText, intval($number) + 1);
    }

    public function emailLogs()
    {
        return $this->morphMany('App\Models\EmailLog', 'mailable');
    }

    public function items()
    {
        return $this->hasMany('Crater\Models\InvoiceItem');
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo('Crater\Models\User', 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo('Crater\Models\User', 'creator_id');
    }

    public function getInvoicePdfUrlAttribute()
    {
        return url('/invoices/pdf/'.$this->unique_hash);
    }

    public function getPreviousStatus()
    {
        if ($this->due_date < Carbon::now()) {
            return self::STATUS_OVERDUE;
        } elseif ($this->viewed) {
            return self::STATUS_VIEWED;
        } elseif ($this->sent) {
            return self::STATUS_SENT;
        } else {
            return self::STATUS_DRAFT;
        }
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

    public function getInvoiceNumAttribute()
    {
        $position = $this->strposX($this->invoice_number, "-", 1) + 1;

        return substr($this->invoice_number, $position);
    }

    public function getInvoicePrefixAttribute()
    {
        $prefix = explode("-", $this->invoice_number)[0];

        return $prefix;
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function getFormattedDueDateAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->due_date)->format($dateFormat);
    }

    public function getFormattedInvoiceDateAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->invoice_date)->format($dateFormat);
    }

    public function scopeWhereStatus($query, $status)
    {
        return $query->where('invoices.status', $status);
    }

    public function scopeWherePaidStatus($query, $status)
    {
        return $query->where('invoices.paid_status', $status);
    }

    public function scopeWhereDueStatus($query, $status)
    {
        return $query->whereIn('invoices.paid_status', [
            self::STATUS_UNPAID,
            self::STATUS_PARTIALLY_PAID,
        ]);
    }

    public function scopeWhereInvoiceNumber($query, $invoiceNumber)
    {
        return $query->where('invoices.invoice_number', 'LIKE', '%'.$invoiceNumber.'%');
    }

    public function scopeInvoicesBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'invoices.invoice_date',
            [$start->format('Y-m-d'), $end->format('Y-m-d')]
        );
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

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);
        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('status')) {
            if (
                $filters->get('status') == self::STATUS_UNPAID ||
                $filters->get('status') == self::STATUS_PARTIALLY_PAID ||
                $filters->get('status') == self::STATUS_PAID
            ) {
                $query->wherePaidStatus($filters->get('status'));
            } elseif ($filters->get('status') == self::STATUS_DUE) {
                $query->whereDueStatus($filters->get('status'));
            } else {
                $query->whereStatus($filters->get('status'));
            }
        }

        if ($filters->get('paid_status')) {
            $query->wherePaidStatus($filters->get('status'));
        }

        if ($filters->get('invoice_id')) {
            $query->whereInvoice($filters->get('invoice_id'));
        }

        if ($filters->get('invoice_number')) {
            $query->whereInvoiceNumber($filters->get('invoice_number'));
        }

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->invoicesBetween($start, $end);
        }

        if ($filters->get('customer_id')) {
            $query->whereCustomer($filters->get('customer_id'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'invoice_number';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereInvoice($query, $invoice_id)
    {
        $query->orWhere('id', $invoice_id);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('invoices.company_id', $company_id);
    }

    public function scopeWhereCustomer($query, $customer_id)
    {
        $query->where('invoices.user_id', $customer_id);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public static function createInvoice($request)
    {
        $data = $request->except('items', 'taxes');

        $data['creator_id'] = Auth::id();
        $data['status'] = Invoice::STATUS_DRAFT;
        $data['company_id'] = $request->header('company');
        $data['paid_status'] = Invoice::STATUS_UNPAID;
        $data['tax_per_item'] = CompanySetting::getSetting('tax_per_item', $request->header('company')) ?? 'NO ';
        $data['discount_per_item'] = CompanySetting::getSetting('discount_per_item', $request->header('company')) ?? 'NO';
        $data['due_amount'] = $request->total;

        if ($request->has('invoiceSend')) {
            $data['status'] = Invoice::STATUS_SENT;
        }

        $invoice = Invoice::create($data);
        $invoice->unique_hash = Hashids::connection(Invoice::class)->encode($invoice->id);
        $invoice->save();

        self::createItems($invoice, $request);

        if ($request->has('taxes') && (! empty($request->taxes))) {
            self::createTaxes($invoice, $request);
        }

        if ($request->customFields) {
            $invoice->addCustomFields($request->customFields);
        }

        $invoice = Invoice::with([
                'items',
                'user',
                'taxes'
            ])
            ->find($invoice->id);

        return $invoice;
    }

    public function updateInvoice($request)
    {
        $data = $request->except('items');
        $oldAmount = $this->total;

        if ($oldAmount != $request->total) {
            $oldAmount = (int) round($request->total) - (int) $oldAmount;
        } else {
            $oldAmount = 0;
        }

        $data['due_amount'] = ($this->due_amount + $oldAmount);

        if ($data['due_amount'] == 0 && $this->paid_status != Invoice::STATUS_PAID) {
            $data['status'] = Invoice::STATUS_COMPLETED;
            $data['paid_status'] = Invoice::STATUS_PAID;
        } elseif ($this->due_amount < 0 && $this->paid_status != Invoice::STATUS_UNPAID) {
            return response()->json([
                'error' => 'invalid_due_amount',
            ]);
        } elseif ($data['due_amount'] != 0 && $this->paid_status == Invoice::STATUS_PAID) {
            $data['status'] = $this->getPreviousStatus();
            $data['paid_status'] = Invoice::STATUS_PARTIALLY_PAID;
        }

        $this->update($data);


        $this->items()->delete();
        $this->taxes()->delete();

        self::createItems($this, $request);

        if ($request->has('taxes') && (! empty($request->taxes))) {
            self::createTaxes($this, $request);
        }

        if ($request->customFields) {
            $this->updateCustomFields($request->customFields);
        }

        $invoice = Invoice::with([
                'items',
                'user',
                'taxes'
            ])
            ->find($this->id);

        return $invoice;
    }

    public function send($data)
    {
        $data['invoice'] = $this->toArray();
        $data['user'] = $this->user->toArray();
        $data['company'] = Company::find($this->company_id);
        $data['body'] = $this->getEmailBody($data['body']);
        $data['attach']['data'] = ($this->getEmailAttachmentSetting()) ? $this->getPDFData() : null;

        if ($this->status == Invoice::STATUS_DRAFT) {
            $this->status = Invoice::STATUS_SENT;
            $this->sent = true;
            $this->save();
        }

        \Mail::to($data['to'])->send(new SendInvoiceMail($data));

        return [
            'success' => true,
        ];
    }

    public static function createItems($invoice, $request)
    {
        $invoiceItems = $request->items;

        foreach ($invoiceItems as $invoiceItem) {
            $invoiceItem['company_id'] = $request->header('company');
            $item = $invoice->items()->create($invoiceItem);

            if (array_key_exists('taxes', $invoiceItem) && $invoiceItem['taxes']) {
                foreach ($invoiceItem['taxes'] as $tax) {
                    $tax['company_id'] = $request->header('company');
                    if (gettype($tax['amount']) !== "NULL") {
                        $item->taxes()->create($tax);
                    }
                }
            }
        }
    }

    public static function createTaxes($invoice, $request)
    {
        if ($request->has('taxes') && (! empty($request->taxes))) {
            foreach ($request->taxes as $tax) {
                $tax['company_id'] = $request->header('company');

                if (gettype($tax['amount']) !== "NULL") {
                    $invoice->taxes()->create($tax);
                }
            }
        }
    }

    public function getPDFData()
    {
        $taxes = collect();

        if ($this->tax_per_item === 'YES') {
            foreach ($this->items as $item) {
                foreach ($item->taxes as $tax) {
                    $found = $taxes->filter(function ($item) use ($tax) {
                        return $item->tax_type_id == $tax->tax_type_id;
                    })->first();

                    if ($found) {
                        $found->amount += $tax->amount;
                    } else {
                        $taxes->push($tax);
                    }
                }
            }
        }

        $invoiceTemplate = self::find($this->id)->template_name;

        $company = Company::find($this->company_id);
        $locale = CompanySetting::getSetting('language', $company->id);

        App::setLocale($locale);

        $logo = $company->logo_path;

        view()->share([
            'invoice' => $this,
            'company_address' => $this->getCompanyAddress(),
            'shipping_address' => $this->getCustomerShippingAddress(),
            'billing_address' => $this->getCustomerBillingAddress(),
            'notes' => $this->getNotes(),
            'logo' => $logo ?? null,
            'taxes' => $taxes,
        ]);

        return PDF::loadView('app.pdf.invoice.'.$invoiceTemplate);
    }

    public function getEmailAttachmentSetting()
    {
        $invoiceAsAttachment = CompanySetting::getSetting('invoice_email_attachment', $this->company_id);

        if ($invoiceAsAttachment == 'NO') {
            return false;
        }

        return true;
    }

    public function getCompanyAddress()
    {
        if ($this->company && (! $this->company->address()->exists())) {
            return false;
        }

        $format = CompanySetting::getSetting('invoice_company_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getCustomerShippingAddress()
    {
        if ($this->user && (! $this->user->shippingAddress()->exists())) {
            return false;
        }

        $format = CompanySetting::getSetting('invoice_shipping_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getCustomerBillingAddress()
    {
        if ($this->user && (! $this->user->billingAddress()->exists())) {
            return false;
        }

        $format = CompanySetting::getSetting('invoice_billing_address_format', $this->company_id);

        return $this->getFormattedString($format);
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
            '{INVOICE_DATE}' => $this->formattedInvoiceDate,
            '{INVOICE_DUE_DATE}' => $this->formattedDueDate,
            '{INVOICE_NUMBER}' => $this->invoice_number,
            '{INVOICE_REF_NUMBER}' => $this->reference_number,
            '{INVOICE_LINK}' => url('/customer/invoices/pdf/'.$this->unique_hash),
        ];
    }
}
