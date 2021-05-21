<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    public const BILLING_TYPE = 'billing';
    public const SHIPPING_TYPE = 'shipping';

    protected $guarded = ['id'];

    public function getCountryNameAttribute()
    {
        $name = $this->country ? $this->country->name : null;

        return $name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
