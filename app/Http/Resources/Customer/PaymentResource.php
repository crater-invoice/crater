<?php

namespace Crater\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'payment_number' => $this->payment_number,
            'payment_date' => $this->payment_date,
            'notes' => $this->notes,
            'amount' => $this->amount,
            'unique_hash' => $this->unique_hash,
            'invoice_id' => $this->invoice_id,
            'company_id' => $this->company_id,
            'payment_method_id' => $this->payment_method_id,
            'customer_id' => $this->customer_id,
            'exchange_rate' => $this->exchange_rate,
            'base_amount' => $this->base_amount,
            'currency_id' => $this->currency_id,
            'transaction_id' => $this->transaction_id,
            'formatted_created_at' => $this->formattedCreatedAt,
            'formatted_payment_date' => $this->formattedPaymentDate,
            'payment_pdf_url' => $this->paymentPdfUrl,
            'customer' => $this->when($this->customer()->exists(), function () {
                return new CustomerResource($this->customer);
            }),
            'invoice' => $this->when($this->invoice()->exists(), function () {
                return new InvoiceResource($this->invoice);
            }),
            'payment_method' => $this->when($this->paymentMethod()->exists(), function () {
                return new PaymentMethodResource($this->paymentMethod);
            }),
            'fields' => $this->when($this->fields()->exists(), function () {
                return CustomFieldValueResource::collection($this->fields);
            }),
            'company' => $this->when($this->company()->exists(), function () {
                return new CompanyResource($this->company);
            }),
            'currency' => $this->when($this->currency()->exists(), function () {
                return new CurrencyResource($this->currency);
            }),
            'transaction' => $this->when($this->transaction()->exists(), function () {
                return new TransactionResource($this->transaction);
            }),
        ];
    }
}
