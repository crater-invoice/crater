<?php
namespace Crater;

use Illuminate\Database\Eloquent\Model;
use Crater\State;
use Crater\Country;

class City extends Model
{
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function address()
    {
        return $this->hasMany(Address::class);
    }
}
