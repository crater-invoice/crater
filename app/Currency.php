<?php
namespace Crater;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'precision',
        'thousand_separator',
        'decimal_separator',
        'position'
    ];
}
