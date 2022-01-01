<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileDiskResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'driver' => $this->driver,
            'set_as_default' => $this->set_as_default,
            'credentials' => $this->credentials,
            'company_id' => $this->company_id,
        ];
    }
}
