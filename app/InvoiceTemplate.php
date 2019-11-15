<?php
namespace Crater;

use Illuminate\Database\Eloquent\Model;
use Crater\Invoice;

class InvoiceTemplate extends Model
{
    protected $fillable = ['path', 'view', 'name'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getPathAttribute($value)
    {
        return url($value);
    }
}
