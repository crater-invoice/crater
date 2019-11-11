<?php
namespace Laraspace;

use Illuminate\Database\Eloquent\Model;
use Laraspace\State;
use Laraspace\Country;

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
