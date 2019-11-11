<?php
namespace Laraspace;

use Illuminate\Database\Eloquent\Model;
use Laraspace\State;
use Laraspace\Country;

class Country extends Model
{
    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function address()
    {
        return $this->hasMany(Address::class);
    }
}
