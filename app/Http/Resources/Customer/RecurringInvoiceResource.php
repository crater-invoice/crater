<?php

namespace Crater\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class RecurringInvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'starts_at' => $this->starts_at,
            'formatted_starts_at' => $this->formattedStartsAt,
            'formatted_created_at' => $this->formattedCreatedAt,
            'formatted_next_invoice_at' => $this->formattedNextInvoiceAt,
            'formatted_limit_date' => $this->formattedLimitDate,
            'send_automatically' => $this->send_automatically,
            'customer_id' => $this->customer_id,
            'company_id' => $this->company_id,
            'status' => $this->status,
            'next_invoice_at' => $this->next_invoice_at,
            'frequency' => $this->frequency,
            'limit_by' => $this->limit_by,
            'limit_count' => $this->limit_count,
            'limit_date' => $this->limit_date,
            'exchange_rate' => $this->exchange_rate,
            'tax_per_item' => $this->tax_per_item,
            'discount_per_item' => $this->discount_per_item,
            'notes' => $this->notes,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'discount_val' => $this->discount_val,
            'sub_total' => $this->sub_total,
            'total' => $this->total,
            'tax' => $this->tax,
            'due_amount' => $this->due_amount,
            'template_name' => $this->template_name,
            'fields' => $this->when($this->fields()->exists(), function () {
                return CustomFieldValueResource::collection($this->fields);
            }),
            'items' => $this->when($this->items()->exists(), function () {
                return InvoiceItemResource::collection($this->items);
            }),
            'customer' => $this->when($this->customer()->exists(), function () {
                return new CustomerResource($this->customer);
            }),
            'company' => $this->when($this->company()->exists(), function () {
                return new CompanyResource($this->company);
            }),
            'invoices' => $this->when($this->invoices()->exists(), function () {
                return InvoiceResource::collection($this->invoices);
            }),
            'taxes' => $this->when($this->taxes()->exists(), function () {
                return TaxResource::collection($this->taxes);
            }),
            'currency' => $this->when($this->currency()->exists(), function () {
                return new CurrencyResource($this->currency);
            }),
        ];
    }
}
