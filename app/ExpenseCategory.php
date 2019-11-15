<?php
namespace Crater;

use Illuminate\Database\Eloquent\Model;
use Crater\Expense;
use Carbon\Carbon;

class ExpenseCategory extends Model
{
    protected $fillable = ['name', 'company_id', 'description'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['amount', 'formattedCreatedAt'];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);
        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function getAmountAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }
}
