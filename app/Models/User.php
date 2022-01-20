<?php

namespace Crater\Models;

use Carbon\Carbon;
use Crater\Http\Requests\UserRequest;
use Crater\Notifications\MailResetPasswordNotification;
use Crater\Traits\HasCustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use Notifiable;
    use InteractsWithMedia;
    use HasCustomFieldsTrait;
    use HasFactory;
    use HasRolesAndAbilities;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $with = [
        'currency',
    ];

    protected $appends = [
        'formattedCreatedAt',
        'avatar',
    ];

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('email', $username)->first();
    }

    public function setPasswordAttribute($value)
    {
        if ($value != null) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function isSuperAdminOrAdmin()
    {
        return ($this->role == 'super admin') || ($this->role == 'admin');
    }

    public static function login($request)
    {
        $remember = $request->remember;
        $email = $request->email;
        $password = $request->password;

        return (\Auth::attempt(['email' => $email, 'password' => $password], $remember));
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', request()->header('company'));

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class, 'creator_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'creator_id');
    }

    public function recurringInvoices()
    {
        return $this->hasMany(RecurringInvoice::class, 'creator_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function creator()
    {
        return $this->belongsTo('Crater\Models\User', 'creator_id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'user_company', 'user_id', 'company_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'creator_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'creator_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'creator_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'creator_id');
    }

    public function settings()
    {
        return $this->hasMany(UserSetting::class, 'user_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function billingAddress()
    {
        return $this->hasOne(Address::class)->where('type', Address::BILLING_TYPE);
    }

    public function shippingAddress()
    {
        return $this->hasOne(Address::class)->where('type', Address::SHIPPING_TYPE);
    }

    /**
     * Override the mail body for reset password notification mail.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordNotification($token));
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where(function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%')
                    ->orWhere('email', 'LIKE', '%'.$term.'%')
                    ->orWhere('phone', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopeWhereContactName($query, $contactName)
    {
        return $query->where('contact_name', 'LIKE', '%'.$contactName.'%');
    }

    public function scopeWhereDisplayName($query, $displayName)
    {
        return $query->where('name', 'LIKE', '%'.$displayName.'%');
    }

    public function scopeWherePhone($query, $phone)
    {
        return $query->where('phone', 'LIKE', '%'.$phone.'%');
    }

    public function scopeWhereEmail($query, $email)
    {
        return $query->where('email', 'LIKE', '%'.$email.'%');
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return $query->get();
        }

        return $query->paginate($limit);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('display_name')) {
            $query->whereDisplayName($filters->get('display_name'));
        }

        if ($filters->get('email')) {
            $query->whereEmail($filters->get('email'));
        }

        if ($filters->get('phone')) {
            $query->wherePhone($filters->get('phone'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereSuperAdmin($query)
    {
        $query->orWhere('role', 'super admin');
    }

    public function scopeApplyInvoiceFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->invoicesBetween($start, $end);
        }
    }

    public function scopeInvoicesBetween($query, $start, $end)
    {
        $query->whereHas('invoices', function ($query) use ($start, $end) {
            $query->whereBetween(
                'invoice_date',
                [$start->format('Y-m-d'), $end->format('Y-m-d')]
            );
        });
    }

    public function getAvatarAttribute()
    {
        $avatar = $this->getMedia('admin_avatar')->first();

        if ($avatar) {
            return  asset($avatar->getUrl());
        }

        return 0;
    }

    public function setSettings($settings)
    {
        foreach ($settings as $key => $value) {
            $this->settings()->updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'key' => $key,
                    'value' => $value,
                ]
            );
        }
    }

    public function hasCompany($company_id)
    {
        $companies = $this->companies()->pluck('company_id')->toArray();

        return in_array($company_id, $companies);
    }

    public function getAllSettings()
    {
        return $this->settings()->get()->mapWithKeys(function ($item) {
            return [$item['key'] => $item['value']];
        });
    }

    public function getSettings($settings)
    {
        return $this->settings()->whereIn('key', $settings)->get()->mapWithKeys(function ($item) {
            return [$item['key'] => $item['value']];
        });
    }

    public function isOwner()
    {
        if (Schema::hasColumn('companies', 'owner_id')) {
            $company = Company::find(request()->header('company'));

            if ($company && $this->id == $company->owner_id) {
                return true;
            }
        } else {
            return $this->role == 'super admin' || $this->role == 'admin';
        }

        return false;
    }

    public static function createFromRequest(UserRequest $request)
    {
        $user = self::create($request->getUserPayload());

        $user->setSettings([
            'language' => CompanySetting::getSetting('language', $request->header('company')),
        ]);

        $companies = collect($request->companies);
        $user->companies()->sync($companies->pluck('id'));

        foreach ($companies as $company) {
            BouncerFacade::scope()->to($company['id']);

            BouncerFacade::sync($user)->roles([$company['role']]);
        }

        return $user;
    }

    public function updateFromRequest(UserRequest $request)
    {
        $this->update($request->getUserPayload());

        $companies = collect($request->companies);
        $this->companies()->sync($companies->pluck('id'));

        foreach ($companies as $company) {
            BouncerFacade::scope()->to($company['id']);

            BouncerFacade::sync($this)->roles([$company['role']]);
        }

        return $this;
    }

    public function checkAccess($data)
    {
        if ($this->isOwner()) {
            return true;
        }

        if ((! $data->data['owner_only']) && empty($data->data['ability'])) {
            return true;
        }

        if ((! $data->data['owner_only']) && (! empty($data->data['ability'])) && (! empty($data->data['model'])) && $this->can($data->data['ability'], $data->data['model'])) {
            return true;
        }

        if ((! $data->data['owner_only']) && $this->can($data->data['ability'])) {
            return true;
        }

        return false;
    }

    public static function deleteUsers($ids)
    {
        foreach ($ids as $id) {
            $user = self::find($id);

            if ($user->invoices()->exists()) {
                $user->invoices()->update(['creator_id' => null]);
            }

            if ($user->estimates()->exists()) {
                $user->estimates()->update(['creator_id' => null]);
            }

            if ($user->customers()->exists()) {
                $user->customers()->update(['creator_id' => null]);
            }

            if ($user->recurringInvoices()->exists()) {
                $user->recurringInvoices()->update(['creator_id' => null]);
            }

            if ($user->expenses()->exists()) {
                $user->expenses()->update(['creator_id' => null]);
            }

            if ($user->payments()->exists()) {
                $user->payments()->update(['creator_id' => null]);
            }

            if ($user->items()->exists()) {
                $user->items()->update(['creator_id' => null]);
            }

            if ($user->settings()->exists()) {
                $user->settings()->delete();
            }

            $user->delete();
        }

        return true;
    }
}
