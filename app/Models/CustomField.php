<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $appends = [
        'defaultAnswer',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function setDateAnswerAttribute($value)
    {
        if ($value && $value != null) {
            $this->attributes['date_answer'] = Carbon::createFromFormat('Y-m-d', $value);
        }
    }

    public function setTimeAnswerAttribute($value)
    {
        if ($value && $value != null) {
            $this->attributes['time_answer'] = date("H:i:s", strtotime($value));
        }
    }

    public function setDateTimeAnswerAttribute($value)
    {
        if ($value && $value != null) {
            $this->attributes['date_time_answer'] = Carbon::createFromFormat('Y-m-d H:i', $value);
        }
    }

    public function setOptionsAttribute($value)
    {
        $this->attributes['options'] = json_encode($value);
    }

    public function getDefaultAnswerAttribute()
    {
        $value_type = getCustomFieldValueKey($this->type);

        return $this->$value_type;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customFieldValue()
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('custom_fields.company_id', $company_id);
    }

    public function scopeWhereSearch($query, $search)
    {
        $query->where(function ($query) use ($search) {
            $query->where('label', 'LIKE', '%'.$search.'%')
                ->orWhere('name', 'LIKE', '%'.$search.'%');
        });
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('type')) {
            $query->whereType($filters->get('type'));
        }

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }
    }

    public function scopeWhereType($query, $type)
    {
        $query->where('custom_fields.model_type', $type);
    }

    public static function createCustomField($request)
    {
        $data = $request->validated();
        $data[getCustomFieldValueKey($request->type)] = $request->default_answer;
        $data['company_id'] = $request->header('company');
        $data['slug'] = clean_slug($request->model_type, $request->label);

        return CustomField::create($data);
    }

    public function updateCustomField($request)
    {
        $oldSlug = $this->slug;
        $data = $request->validated();
        $data[getCustomFieldValueKey($request->type)] = $request->default_answer;
        $data['slug'] = clean_slug($request->model_type, $request->label, $this->id);
        $this->update($data);

        if ($oldSlug !== $data['slug']) {
            $settings = [
                'invoice_company_address_format',
                'invoice_shipping_address_format',
                'invoice_billing_address_format',
                'estimate_company_address_format',
                'estimate_shipping_address_format',
                'estimate_billing_address_format',
                'payment_company_address_format',
                'payment_from_customer_address_format',
            ];

            $settings = CompanySetting::getSettings($settings, $this->company_id);

            foreach ($settings as $key => $value) {
                $settings[$key] = str_replace($oldSlug, $data['slug'], $value);
            }

            CompanySetting::setSettings($settings, $this->company_id);
        }

        return $this;
    }
}
