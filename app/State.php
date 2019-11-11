<?php
namespace Laraspace;

use Illuminate\Database\Eloquent\Model;
use Laraspace\City;
use Laraspace\Country;
use Laraspace\Address;

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
