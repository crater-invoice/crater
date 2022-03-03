<?php

namespace Crater\Models;

use Carbon\Carbon;
use Crater\Http\Requests\RecurringInvoiceRequest;
use Crater\Services\SerialNumberFormatter;
use Crater\Traits\HasCustomFieldsTrait;
use Cron;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class RecurringInvoice extends Model
{
    use HasFactory;
    use HasCustomFieldsTrait;

    protected $guarded = [
        'id',
    ];

    protected $dates = [
        'starts_at'
    ];

    public const NONE = 'NONE';
    public const COUNT = 'COUNT';
    public const DATE = 'DATE';

    public const COMPLETED = 'COMPLETED';
    public const ON_HOLD = 'ON_HOLD';
    public const ACTIVE = 'ACTIVE';

    protected $appends = [
        'formattedCreatedAt',
        'formattedStartsAt',
        'formattedNextInvoiceAt',
        'formattedLimitDate'
    ];

    protected $casts = [
        'exchange_rate' => 'float',
        'send_automatically' => 'boolean'
    ];

    public function getFormattedStartsAtAttribute()
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->starts_at)->format($dateFormat);
    }

    public function getFormattedNextInvoiceAtAttribute()
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->next_invoice_at)->format($dateFormat);
    }

    public function getFormattedLimitDateAttribute()
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->limit_date)->format($dateFormat);
    }

    public function getFormattedCreatedAtAttribute()
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function scopeWhereCompany($query)
    {
        $query->where('recurring_invoices.company_id', request()->header('company'));
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return $query->get();
        }

        return $query->paginate($limit);
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereStatus($query, $status)
    {
        return $query->where('recurring_invoices.status', $status);
    }

    public function scopeWhereCustomer($query, $customer_id)
    {
        $query->where('customer_id', $customer_id);
    }

    public function scopeRecurringInvoicesStartBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'starts_at',
            [$start->format('Y-m-d'), $end->format('Y-m-d')]
        );
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->whereHas('customer', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%')
                    ->orWhere('contact_name', 'LIKE', '%'.$term.'%')
                    ->orWhere('company_name', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('status') && $filters->get('status') !== 'ALL') {
            $query->whereStatus($filters->get('status'));
        }

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->recurringInvoicesStartBetween($start, $end);
        }

        if ($filters->get('customer_id')) {
            $query->whereCustomer($filters->get('customer_id'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'created_at';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public static function createFromRequest(RecurringInvoiceRequest $request)
    {
        $recurringInvoice = self::create($request->getRecurringInvoicePayload());

        $company_currency = CompanySetting::getSetting('currency', $request->header('company'));

        if ((string)$recurringInvoice['currency_id'] !== $company_currency) {
            ExchangeRateLog::addExchangeRateLog($recurringInvoice);
        }

        self::createItems($recurringInvoice, $request->items);

        if ($request->has('taxes') && (! empty($request->taxes))) {
            self::createTaxes($recurringInvoice, $request->taxes);
        }

        if ($request->customFields) {
            $recurringInvoice->addCustomFields($request->customFields);
        }

        return $recurringInvoice;
    }

    public function updateFromRequest(RecurringInvoiceRequest $request)
    {
        $data = $request->getRecurringInvoicePayload();

        $this->update($data);

        $company_currency = CompanySetting::getSetting('currency', $request->header('company'));

        if ((string)$data['currency_id'] !== $company_currency) {
            ExchangeRateLog::addExchangeRateLog($this);
        }

        $this->items()->delete();
        self::createItems($this, $request->items);

        $this->taxes()->delete();
        if ($request->has('taxes') && (! empty($request->taxes))) {
            self::createTaxes($this, $request->taxes);
        }

        if ($request->customFields) {
            $this->updateCustomFields($request->customFields);
        }

        return $this;
    }

    public static function createItems($recurringInvoice, $invoiceItems)
    {
        foreach ($invoiceItems as $invoiceItem) {
            $invoiceItem['company_id'] = $recurringInvoice->company_id;
            $item = $recurringInvoice->items()->create($invoiceItem);
            if (array_key_exists('taxes', $invoiceItem) && $invoiceItem['taxes']) {
                foreach ($invoiceItem['taxes'] as $tax) {
                    $tax['company_id'] = $recurringInvoice->company_id;
                    if (gettype($tax['amount']) !== "NULL") {
                        $item->taxes()->create($tax);
                    }
                }
            }
        }
    }

    public static function createTaxes($recurringInvoice, $taxes)
    {
        foreach ($taxes as $tax) {
            $tax['company_id'] = $recurringInvoice->company_id;

            if (gettype($tax['amount']) !== "NULL") {
                $recurringInvoice->taxes()->create($tax);
            }
        }
    }

    public function generateInvoice()
    {
        if (Carbon::now()->lessThan($this->starts_at)) {
            return;
        }

        if ($this->limit_by == 'DATE') {
            $startDate = Carbon::today()->format('Y-m-d');

            $endDate = $this->limit_date;

            if ($endDate >= $startDate) {
                $this->createInvoice();

                $this->updateNextInvoiceDate();
            } else {
                $this->markStatusAsCompleted();
            }
        } elseif ($this->limit_by == 'COUNT') {
            $invoiceCount = Invoice::where('recurring_invoice_id', $this->id)->count();

            if ($invoiceCount < $this->limit_count) {
                $this->createInvoice();

                $this->updateNextInvoiceDate();
            } else {
                $this->markStatusAsCompleted();
            }
        } else {
            $this->createInvoice();

            $this->updateNextInvoiceDate();
        }
    }

    public function createInvoice()
    {
        //get invoice_number
        $serial = (new SerialNumberFormatter())
            ->setModel(new Invoice())
            ->setCompany($this->company_id)
            ->setCustomer($this->customer_id)
            ->setNextNumbers();

        $days = CompanySetting::getSetting('invoice_due_date_days', $this->company_id);

        if (! $days || $days == "null") {
            $days = 7;
        }

        $newInvoice['creator_id'] = $this->creator_id;
        $newInvoice['invoice_date'] = Carbon::today()->format('Y-m-d');
        $newInvoice['due_date'] = Carbon::today()->addDays($days)->format('Y-m-d');
        $newInvoice['status'] = Invoice::STATUS_DRAFT;
        $newInvoice['company_id'] = $this->company_id;
        $newInvoice['paid_status'] = Invoice::STATUS_UNPAID;
        $newInvoice['sub_total'] = $this->sub_total;
        $newInvoice['tax_per_item'] = $this->tax_per_item;
        $newInvoice['discount_per_item'] = $this->discount_per_item;
        $newInvoice['tax'] = $this->tax;
        $newInvoice['total'] = $this->total;
        $newInvoice['customer_id'] = $this->customer_id;
        $newInvoice['currency_id'] = Customer::find($this->customer_id)->currency_id;
        $newInvoice['template_name'] = $this->template_name;
        $newInvoice['due_amount'] = $this->total;
        $newInvoice['recurring_invoice_id'] = $this->id;
        $newInvoice['discount_val'] = $this->discount_val;
        $newInvoice['discount'] = $this->discount;
        $newInvoice['discount_type'] = $this->discount_type;
        $newInvoice['notes'] = $this->notes;
        $newInvoice['exchange_rate'] = $this->exchange_rate;
        $newInvoice['sales_tax_type'] = $this->sales_tax_type;
        $newInvoice['sales_tax_address_type'] = $this->sales_tax_address_type;
        $newInvoice['invoice_number'] = $serial->getNextNumber();
        $newInvoice['sequence_number'] = $serial->nextSequenceNumber;
        $newInvoice['customer_sequence_number'] = $serial->nextCustomerSequenceNumber;
        $newInvoice['base_due_amount'] = $this->exchange_rate * $this->due_amount;
        $newInvoice['base_discount_val'] = $this->exchange_rate * $this->discount_val;
        $newInvoice['base_sub_total'] = $this->exchange_rate * $this->sub_total;
        $newInvoice['base_tax'] = $this->exchange_rate * $this->tax;
        $newInvoice['base_total'] = $this->exchange_rate * $this->total;
        $invoice = Invoice::create($newInvoice);
        $invoice->unique_hash = Hashids::connection(Invoice::class)->encode($invoice->id);
        $invoice->save();

        $this->load('items.taxes');
        Invoice::createItems($invoice, $this->items->toArray());

        if ($this->taxes()->exists()) {
            Invoice::createTaxes($invoice, $this->taxes->toArray());
        }

        if ($this->fields()->exists()) {
            $customField = [];

            foreach ($this->fields as $data) {
                $customField[] = [
                    'id' => $data->custom_field_id,
                    'value' => $data->defaultAnswer
                ];
            }

            $invoice->addCustomFields($customField);
        }

        //send automatically
        if ($this->send_automatically == true) {
            $data = [
                'body' => CompanySetting::getSetting('invoice_mail_body', $this->company_id),
                'from' => config('mail.from.address'),
                'to' => $this->customer->email,
                'subject' => 'New Invoice',
                'invoice' => $invoice->toArray(),
                'customer' => $invoice->customer->toArray(),
                'company' => Company::find($invoice->company_id)
            ];

            $invoice->send($data);
        }
    }

    public function markStatusAsCompleted()
    {
        if ($this->status == $this->status) {
            $this->status = self::COMPLETED;
            $this->save();
        }
    }

    public static function getNextInvoiceDate($frequency, $starts_at)
    {
        $cron = new Cron\CronExpression($frequency);

        return $cron->getNextRunDate($starts_at)->format('Y-m-d H:i:s');
    }

    public function updateNextInvoiceDate()
    {
        $nextInvoiceAt = self::getNextInvoiceDate($this->frequency, $this->starts_at);

        $this->next_invoice_at = $nextInvoiceAt;
        $this->save();
    }

    public static function deleteRecurringInvoice($ids)
    {
        foreach ($ids as $id) {
            $recurringInvoice = self::find($id);

            if ($recurringInvoice->invoices()->exists()) {
                $recurringInvoice->invoices()->update(['recurring_invoice_id' => null]);
            }

            if ($recurringInvoice->items()->exists()) {
                $recurringInvoice->items()->delete();
            }

            if ($recurringInvoice->taxes()->exists()) {
                $recurringInvoice->taxes()->delete();
            }

            $recurringInvoice->delete();
        }

        return true;
    }
}
