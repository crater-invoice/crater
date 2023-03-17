<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MailSenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'driver' => $this->driver,
            'is_default' => $this->is_default,
            'bcc' => $this->bcc,
            'cc' => $this->cc,
            'from_address' => $this->from_address,
            'from_name' => $this->from_name,
            'company_id' => $this->company_id,
            'settings' => $this->settings
        ];
    }
}
