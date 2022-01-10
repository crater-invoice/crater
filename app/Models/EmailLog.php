<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function mailable()
    {
        return $this->morphTo();
    }

    public function isExpired()
    {
        $linkexpiryDays = CompanySetting::getSetting('link_expiry_days', $this->mailable()->get()->toArray()[0]['company_id']);
        $checkExpiryLinks = CompanySetting::getSetting('automatically_expire_public_links', $this->mailable()->get()->toArray()[0]['company_id']);

        $expiryDate = $this->created_at->addDays($linkexpiryDays);

        if ($checkExpiryLinks == 'YES' && Carbon::now()->format('Y-m-d') > $expiryDate->format('Y-m-d')) {
            return true;
        }

        return false;
    }
}
