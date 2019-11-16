<?php
namespace Crater;

use Illuminate\Database\Eloquent\Model;
use Crater\State;

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
