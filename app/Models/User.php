<?php

namespace Crater\Models;

use Carbon\Carbon;
use Crater\Notifications\MailResetPasswordNotification;
use Crater\Traits\HasCustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use Notifiable;
    use InteractsWithMedia;
    use HasCustomFieldsTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'company_id',
        'password',
        'facebook_id',
        'currency_id',
        'google_id',
        'github_id',
        'role',
        'group_id',
        'phone',
        'company_name',
        'contact_name',
        'website',
        'enable_portal',
        'creator_id',
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
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator()
    {
        return $this->belongsTo('Crater\Models\User', 'creator_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function billingAddress()
    {
        return $this->hasOne(Address::class)->where('type', Address::BILLING_TYPE);
    }

    public function shippingAddress()
    {
        return $this->hasOne(Address::class)->where('type', Address::SHIPPING_TYPE);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function settings()
    {
        return $this->hasMany(UserSetting::class, 'user_id');
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

    public function scopeCustomer($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('contact_name')) {
            $query->whereContactName($filters->get('contact_name'));
        }

        if ($filters->get('display_name')) {
            $query->whereDisplayName($filters->get('display_name'));
        }

        if ($filters->get('email')) {
            $query->whereEmail($filters->get('email'));
        }

        if ($filters->get('customer_id')) {
            $query->whereCustomer($filters->get('customer_id'));
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

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('users.company_id', $company_id);
    }

    public function scopeWhereCustomer($query, $customer_id)
    {
        $query->orWhere('users.id', $customer_id);
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

    public static function deleteCustomers($ids)
    {
        foreach ($ids as $id) {
            $customer = self::find($id);

            if ($customer->estimates()->exists()) {
                $customer->estimates()->delete();
            }

            if ($customer->invoices()->exists()) {
                $customer->invoices()->delete();
            }

            if ($customer->payments()->exists()) {
                $customer->payments()->delete();
            }

            if ($customer->addresses()->exists()) {
                $customer->addresses()->delete();
            }

            $customer->delete();
        }

        return true;
    }

    public function getAvatarAttribute()
    {
        $avatar = $this->getMedia('admin_avatar')->first();

        if ($avatar) {
            return  asset($avatar->getUrl());
        }

        return 0;
    }

    public static function createCustomer($request)
    {
        $data = $request->only([
            'name',
            'email',
            'phone',
            'company_name',
            'contact_name',
            'website',
            'enable_portal',
        ]);

        $data['creator_id'] = Auth::id();
        $data['company_id'] = $request->header('company');
        $data['role'] = 'customer';
        $data['password'] = Hash::make($request->password);
        $customer = User::create($data);

        $customer['currency_id'] = $request->currency_id;
        $customer->save();

        if ($request->addresses) {
            foreach ($request->addresses as $address) {
                $customer->addresses()->create($address);
            }
        }

        $customFields = $request->customFields;

        if ($customFields) {
            $customer->addCustomFields($customFields);
        }

        $customer = User::with('billingAddress', 'shippingAddress', 'fields')->find($customer->id);

        return $customer;
    }

    public static function updateCustomer($request, $customer)
    {
        $data = $request->only([
            'name',
            'currency_id',
            'email',
            'phone',
            'company_name',
            'contact_name',
            'website',
            'enable_portal',
        ]);

        $data['role'] = 'customer';
        if ($request->has('password')) {
            $customer->password = Hash::make($request->password);
        }
        $customer->update($data);

        $customer->addresses()->delete();
        if ($request->addresses) {
            foreach ($request->addresses as $address) {
                $customer->addresses()->create($address);
            }
        }

        $customFields = $request->customFields;

        if ($customFields) {
            $customer->updateCustomFields($customFields);
        }

        $customer = User::with('billingAddress', 'shippingAddress', 'fields')->find($customer->id);

        return $customer;
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

    public function getSettings($settings)
    {
        $settings = $this->settings()->whereIn('key', $settings)->get();
        $companySettings = [];

        foreach ($settings as $setting) {
            $companySettings[$setting->key] = $setting->value;
        }

        return $companySettings;
    }
}
