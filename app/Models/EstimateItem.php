<?php

namespace InvoiceShelf\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvoiceShelf\Traits\HasCustomFieldsTrait;

class EstimateItem extends Model
{
    use HasCustomFieldsTrait;
    use HasFactory;

    protected $guarded = [
        'id',
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
