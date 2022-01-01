<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tax extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'amount' => 'integer',
        'percent' => 'float',
    ];

    public function taxType()
    {
        return $this->belongsTo(TaxType::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function recurringInvoice()
    {
        return $this->belongsTo(RecurringInvoice::class);
    }

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    public function estimateItem()
    {
        return $this->belongsTo(EstimateItem::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopeTaxAttributes($query)
    {
        $query->select(
            DB::raw('sum(base_amount) as total_tax_amount, tax_type_id')
        )->groupBy('tax_type_id');
    }

    public function scopeInvoicesBetween($query, $start, $end)
    {
        $query->whereHas('invoice', function ($query) use ($start, $end) {
            $query->where('paid_status', Invoice::STATUS_PAID)
                ->whereBetween(
                    'invoice_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                );
        })
            ->orWhereHas('invoiceItem.invoice', function ($query) use ($start, $end) {
                $query->where('paid_status', Invoice::STATUS_PAID)
                    ->whereBetween(
                        'invoice_date',
                        [$start->format('Y-m-d'), $end->format('Y-m-d')]
                    );
            });
    }

    public function scopeWhereInvoicesFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));

            $query->invoicesBetween($start, $end);
        }
    }
}
