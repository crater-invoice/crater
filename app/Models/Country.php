<?php
namespace Crater\Models;

use Crater\Models\Address;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    public function address()
    {
        return $this->hasMany(Address::class);
    }
}
