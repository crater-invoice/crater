<?php
namespace Crater;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = ['company_id', 'option', 'value'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public static function setSetting($key, $setting, $company_id)
    {
        $old = self::whereOption($key)->whereCompany($company_id)->first();

        if ($old) {
            $old->value = $setting;
            $old->save();
            return;
        }

        $set = new CompanySetting();
        $set->option = $key;
        $set->value = $setting;
        $set->company_id = $company_id;
        $set->save();
    }

    public static function getSetting($key, $company_id)
    {
        $setting = static::whereOption($key)->whereCompany($company_id)->first();

        if ($setting) {
            return $setting->value;
        } else {
            return null;
        }
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }
}
