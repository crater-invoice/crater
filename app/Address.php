<?php
namespace Laraspace;

use Illuminate\Database\Eloquent\Model;
use Laraspace\User;
use Laraspace\Country;
use Laraspace\State;
use Laraspace\City;

class Address extends Model
{
    const BILLING_TYPE = 'BILLING';
    const SHIPPING_TYPE = 'SHIPPING';

    protected $fillable = [
        'name',
        'address_street_1',
        'address_street_2',
        'city_id',
        'state_id',
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

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
