<?php
namespace Crater;

use Illuminate\Database\Eloquent\Model;
use Crater\City;
use Crater\Country;
use Crater\Address;

class State extends Model
{
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function address()
    {
        return $this->hasMany(Address::class);
    }
}
