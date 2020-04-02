<?php
namespace Crater;

use Illuminate\Database\Eloquent\Model;
use Crater\User;
use Crater\Country;

class Address extends Model
{
    const BILLING_TYPE = 'billing';
    const SHIPPING_TYPE = 'shipping';

    protected $fillable = [
        'name',
        'address_street_1',
        'address_street_2',
        'city',
        'state',
        'country_id',
        'zip',
        'phone',
        'fax',
        'type',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
