<?php
namespace Crater;

use Illuminate\Database\Eloquent\Model;
use Crater\InvoiceItem;
use Crater\EstimateItem;
use Carbon\Carbon;

class Item extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'price',
        'company_id',
        'description'
    ];

    protected $casts = [
        'price' => 'integer'
    ];

    protected $appends = [
        'formattedCreatedAt'
    ];

    public function scopeWhereSearch($query, $search)
    {
        return $query->where('name', 'LIKE', '%'.$search.'%');
    }

    public function scopeWherePrice($query, $price)
    {
        return $query->where('price', $price);
    }

    public function scopeWhereUnit($query, $unit)
    {
        return $query->where('unit', $unit);
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('price')) {
            $query->wherePrice($filters->get('price'));
        }

        if ($filters->get('unit')) {
            $query->whereUnit($filters->get('unit'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);
        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function estimateItems()
    {
        return $this->hasMany(EstimateItem::class);
    }

    public static function deleteItem($id)
    {
        $item = Item::find($id);

        if ($item->taxes()->exists() && $item->taxes()->count() > 0) {
            return false;
        }

        if ($item->invoiceItems()->exists() && $item->invoiceItems()->count() > 0) {
            return false;
        }

        if ($item->estimateItems()->exists() && $item->estimateItems()->count() > 0) {
            return false;
        }

        $item->delete();

        return true;
    }
}
