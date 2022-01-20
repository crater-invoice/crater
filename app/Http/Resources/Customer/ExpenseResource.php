<?php

namespace Crater\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'expense_date' => $this->expense_date,
            'amount' => $this->amount,
            'notes' => $this->notes,
            'customer_id' => $this->customer_id,
            'attachment_receipt_url' => $this->receipt_url,
            'attachment_receipt' => $this->receipt,
            'attachment_receipt_meta' => $this->receipt_meta,
            'company_id' => $this->company_id,
            'expense_category_id' => $this->expense_category_id,
            'formatted_expense_date' => $this->formattedExpenseDate,
            'formatted_created_at' => $this->formattedCreatedAt,
            'exchange_rate' => $this->exchange_rate,
            'currency_id' => $this->currency_id,
            'base_amount' => $this->base_amount,
            'payment_method_id' => $this->payment_method_id,
            'customer' => $this->when($this->customer()->exists(), function () {
                return new CustomerResource($this->customer);
            }),
            'expense_category' => $this->when($this->category()->exists(), function () {
                return new ExpenseCategoryResource($this->category);
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
            'payment_method' => $this->when($this->paymentMethod()->exists(), function () {
                return new PaymentMethodResource($this->paymentMethod);
            }),
        ];
    }
}
