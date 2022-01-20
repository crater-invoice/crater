<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFieldValue extends Model
{
    use HasFactory;

    protected $dates = [
        'date_answer',
        'date_time_answer'
    ];

    protected $guarded = [
        'id',
    ];

    protected $appends = [
        'defaultAnswer',
    ];

    public function setTimeAnswerAttribute($value)
    {
        if ($value && $value != null) {
            $this->attributes['time_answer'] = date("H:i:s", strtotime($value));
        } else {
            $this->attributes['time_answer'] = null;
        }
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

    public function customField()
    {
        return $this->belongsTo(CustomField::class);
    }

    public function customFieldValuable()
    {
        return $this->morphTo();
    }
}
