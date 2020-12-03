<?php
namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;

class Setting extends Model
{
    use HasFactory;

    public static function setSetting($key, $setting)
    {
        $old = self::whereOption($key)->first();

        if ($old) {
            $old->value = $setting;
            $old->save();
            return;
        }

        $set = new Setting();
        $set->option = $key;
        $set->value = $setting;
        $set->save();
    }

    public static function getSetting($key)
    {
        try{
            $setting = static::whereOption($key)->first();
        }catch (Exception $exception){
            $setting = null;
        }

        if ($setting) {
            return $setting->value;
        } else {
            return null;
        }
    }
}
