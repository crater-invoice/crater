<?php

namespace Crater\Models;

use Crater\Http\Requests\MailSenderRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSender extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = json_encode($value);
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'desc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return $query->get();
        }

        return $query->paginate($limit);
    }

    public function scopeWhereCompany($query)
    {
        $query->where('mail_senders.company_id', request()->header('company'));
    }

    public static function createFromRequest(MailSenderRequest $request)
    {
        $senderMail = self::create($request->getMailSenderPayload());

        return $senderMail;
    }

    public function updateFromRequest(MailSenderRequest $request)
    {
        $data = $request->getMailSenderPayload();

        $this->update($data);

        return $this;
    }
}
