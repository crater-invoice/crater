<?php

namespace Crater\Models;

use Carbon\Carbon;
use Crater\Notifications\CustomerMailResetPasswordNotification;
use Crater\Traits\HasCustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Customer extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use Notifiable;
    use InteractsWithMedia;
    use HasCustomFieldsTrait;
    use HasFactory;
    use HasRolesAndAbilities;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $with = [
        'currency',
    ];

    protected $appends = [
        'formattedCreatedAt',
        'avatar'
    ];

    protected $casts = [
        'enable_portal' => 'boolean',
    ];

    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function setPasswordAttribute($value)
    {
        if ($value != null) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function recurringInvoices()
    {
        return $this->hasMany(RecurringInvoice::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator()
    {
        return $this->belongsTo(Customer::class, 'creator_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function billingAddress()
    {
        return $this->hasOne(Address::class)->where('type', Address::BILLING_TYPE);
    }

    public function shippingAddress()
    {
        return $this->hasOne(Address::class)->where('type', Address::SHIPPING_TYPE);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomerMailResetPasswordNotification($token));
    }

    public function getAvatarAttribute()
    {
        $avatar = $this->getMedia('customer_avatar')->first();

        if ($avatar) {
            return  asset($avatar->getUrl());
        }

        return 0;
    }

    public static function deleteCustomers($ids)
    {
        foreach ($ids as $id) {
            $customer = self::find($id);

            if ($customer->estimates()->exists()) {
                $customer->estimates()->delete();
            }

            if ($customer->invoices()->exists()) {
                $customer->invoices->map(function ($invoice) {
                    if ($invoice->transactions()->exists()) {
                        $invoice->transactions()->delete();
                    }
                    $invoice->delete();
                });
            }

            if ($customer->payments()->exists()) {
                $customer->payments()->delete();
            }

            if ($customer->addresses()->exists()) {
                $customer->addresses()->delete();
            }

            if ($customer->expenses()->exists()) {
                $customer->expenses()->delete();
            }

            if ($customer->recurringInvoices()->exists()) {
                foreach ($customer->recurringInvoices as $recurringInvoice) {
                    if ($recurringInvoice->items()->exists()) {
                        $recurringInvoice->items()->delete();
                    }

                    $recurringInvoice->delete();
                }
            }

            $customer->delete();
        }

        return true;
    }

    public static function createCustomer($request)
    {
        $customer = Customer::create($request->getCustomerPayload());

        if ($request->shipping) {
            if ($request->hasAddress($request->shipping)) {
                $customer->addresses()->create($request->getShippingAddress());
            }
        }

        if ($request->billing) {
            if ($request->hasAddress($request->billing)) {
                $customer->addresses()->create($request->getBillingAddress());
            }
        }

        $customFields = $request->customFields;

        if ($customFields) {
            $customer->addCustomFields($customFields);
        }

        $customer = Customer::with('billingAddress', 'shippingAddress', 'fields')->find($customer->id);

        return $customer;
    }

    public static function updateCustomer($request, $customer)
    {
        $condition = $customer->estimates()->exists() || $customer->invoices()->exists() || $customer->payments()->exists() || $customer->recurringInvoices()->exists();

        if (($customer->currency_id !== $request->currency_id) && $condition) {
            return 'you_cannot_edit_currency';
        }

        $customer->update($request->getCustomerPayload());

        $customer->addresses()->delete();

        if ($request->shipping) {
            if ($request->hasAddress($request->shipping)) {
                $customer->addresses()->create($request->getShippingAddress());
            }
        }

        if ($request->billing) {
            if ($request->hasAddress($request->billing)) {
                $customer->addresses()->create($request->getBillingAddress());
            }
        }

        $customFields = $request->customFields;

        if ($customFields) {
            $customer->updateCustomFields($customFields);
        }

        $customer = Customer::with('billingAddress', 'shippingAddress', 'fields')->find($customer->id);

        return $customer;
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
        return $query->where('customers.company_id', request()->header('company'));
    }

    public function scopeWhereContactName($query, $contactName)
    {
        return $query->where('contact_name', 'LIKE', '%'.$contactName.'%');
    }

    public function scopeWhereDisplayName($query, $displayName)
    {
        return $query->where('name', 'LIKE', '%'.$displayName.'%');
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

    public function scopeWherePhone($query, $phone)
    {
        return $query->where('phone', 'LIKE', '%'.$phone.'%');
    }

    public function scopeWhereCustomer($query, $customer_id)
    {
        $query->orWhere('customers.id', $customer_id);
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
}
