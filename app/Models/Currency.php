<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function recurringInvoices()
    {
        return $this->hasMany(RecurringInvoice::class);
    }

    public function checkTransactions()
    {
        if (
            $this->customers()->exists() ||
            $this->items()->exists() ||
            $this->invoices()->exists() ||
            $this->estimates()->exists() ||
            $this->expenses()->exists() ||
            $this->payments()->exists() ||
            $this->recurringInvoices()->exists()
        ) {
            return true;
        }

        return false;
    }
}
