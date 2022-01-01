<?php

namespace Crater\Models;

use Crater\Traits\HasCustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateItem extends Model
{
    use HasFactory;
    use HasCustomFieldsTrait;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'price' => 'integer',
        'total' => 'integer',
        'discount' => 'float',
        'quantity' => 'float',
        'discount_val' => 'integer',
        'tax' => 'integer',
    ];

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }
}
