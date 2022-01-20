<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxResource extends JsonResource
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
            'tax_type_id' => $this->tax_type_id,
            'invoice_id' => $this->invoice_id,
            'estimate_id' => $this->estimate_id,
            'invoice_item_id' => $this->invoice_item_id,
            'estimate_item_id' => $this->estimate_item_id,
            'item_id' => $this->item_id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'amount' => $this->amount,
            'percent' => $this->percent,
            'compound_tax' => $this->compound_tax,
            'base_amount' => $this->base_amount,
            'currency_id' => $this->currency_id,
            'type' => $this->taxType->type,
            'recurring_invoice_id' => $this->recurring_invoice_id,
            'tax_type' => $this->when($this->taxType()->exists(), function () {
                return new TaxTypeResource($this->taxType);
            }),
            'currency' => $this->when($this->currency()->exists(), function () {
                return new CurrencyResource($this->currency);
            }),
        ];
    }
}
