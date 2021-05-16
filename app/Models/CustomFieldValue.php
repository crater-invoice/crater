<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFieldValue extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $appends = [
        'defaultAnswer',
    ];

    public function setDateAnswerAttribute($value)
    {
        if ($value && $value != null) {
            $this->attributes['date_answer'] = Carbon::createFromFormat('Y-m-d', $value);
        }
        $this->attributes['date_answer'] = null;
    }

    public function setTimeAnswerAttribute($value)
    {
        if ($value && $value != null) {
            $this->attributes['time_answer'] = date("H:i:s", strtotime($value));
        }
        $this->attributes['time_answer'] = null;
    }

    public function setDateTimeAnswerAttribute($value)
    {
        if ($value && $value != null) {
            $this->attributes['date_time_answer'] = Carbon::createFromFormat('Y-m-d H:i', $value);
        }
        $this->attributes['time_answer'] = null;
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
