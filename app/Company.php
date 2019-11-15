<?php
namespace Crater;

use Illuminate\Database\Eloquent\Model;
use Crater\User;
use Crater\CompanySetting;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Company extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $fillable = ['name', 'logo', 'unique_hash'];

    protected $appends=['logo'];

    public function getLogoAttribute()
    {
        $logo = $this->getMedia('logo')->first();
        if ($logo) {
            return  asset($logo->getUrl());
        }
        return ;
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function settings()
    {
        return $this->hasMany(CompanySetting::class);
    }
}
