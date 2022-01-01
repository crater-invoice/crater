<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeRateLogResource extends JsonResource
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
            'company_id' => $this->company_id,
            'base_currency_id' => $this->base_currency_id,
            'currency_id' => $this->currency_id,
            'exchange_rate' => $this->exchange_rate,
        ];
    }
}
