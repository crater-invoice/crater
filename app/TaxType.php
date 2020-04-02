<?php
namespace Crater;

use Illuminate\Database\Eloquent\Model;
use Crater\Tax;

class TaxType extends Model
{
    protected $fillable = [
        'name',
        'percent',
        'company_id',
        'compound_tax',
        'collective_tax',
        'description'
    ];

    protected $casts = [
        'percent' => 'float'
    ];

    public function taxes()
    {
        return $this->hasMany(Tax::class);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }
}
