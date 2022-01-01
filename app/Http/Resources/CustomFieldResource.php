<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomFieldResource extends JsonResource
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
            'slug' => $this->slug,
            'label' => $this->label,
            'model_type' => $this->model_type,
            'type' => $this->type,
            'placeholder' => $this->placeholder,
            'options' => $this->options,
            'boolean_answer' => $this->boolean_answer,
            'date_answer' => $this->date_answer,
            'time_answer' => $this->time_answer,
            'string_answer' => $this->string_answer,
            'number_answer' => $this->number_answer,
            'date_time_answer' => $this->date_time_answer,
            'is_required' => $this->is_required,
            'in_use' => $this->in_use,
            'order' => $this->order,
            'company_id' => $this->company_id,
            'default_answer' => $this->default_answer,
            'company' => $this->when($this->company()->exists(), function () {
                return new CompanyResource($this->company);
            }),
        ];
    }
}
