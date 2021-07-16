<?php

namespace Crater\Models;

use App;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Crater\Mail\SendEstimateMail;
use Crater\Traits\GeneratesPdfTrait;
use Crater\Traits\HasCustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Vinkla\Hashids\Facades\Hashids;

class Estimate extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use GeneratesPdfTrait;
    use HasCustomFieldsTrait;

    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_SENT = 'SENT';
    public const STATUS_VIEWED = 'VIEWED';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_ACCEPTED = 'ACCEPTED';
    public const STATUS_REJECTED = 'REJECTED';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'formattedExpiryDate',
        'formattedEstimateDate',
        'estimatePdfUrl',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'total' => 'integer',
        'tax' => 'integer',
        'sub_total' => 'integer',
        'discount' => 'float',
        'discount_val' => 'integer',
    ];

    public function setEstimateDateAttribute($value)
    {
        if ($value) {
            $this->attributes['estimate_date'] = Carbon::createFromFormat('Y-m-d', $value);
        }
    }

    public function setExpiryDateAttribute($value)
    {
        if ($value) {
            $this->attributes['expiry_date'] = Carbon::createFromFormat('Y-m-d', $value);
        }
    }

    public function getEstimatePdfUrlAttribute()
    {
        return url('/estimates/pdf/'.$this->unique_hash);
    }

    public static function getNextEstimateNumber($value)
    {
        // Get the last created order
        $lastOrder = Estimate::where('estimate_number', 'LIKE', $value.'-%')
            ->orderBy('estimate_number', 'desc')
            ->first();

        // Get number length config
        $numberLength = CompanySetting::getSetting('estimate_number_length', request()->header('company'));
        $numberLengthText = "%0{$numberLength}d";

        if (! $lastOrder) {
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;
        } else {
            $number = explode("-", $lastOrder->estimate_number);
            $number = $number[1];
        }

        // If we have ORD000001 in the database then we only want the number
        // So the substr returns this 000001

        // Add the string in front and higher up the number.
        // the %05d part makes sure that there are always 6 numbers in the string.
        // so it adds the missing zero's when needed.

        return sprintf($numberLengthText, intval($number) + 1);
    }

    public function emailLogs()
    {
        return $this->morphMany('App\Models\EmailLog', 'mailable');
    }

    public function items()
    {
        return $this->hasMany('Crater\Models\EstimateItem');
    }

    public function user()
    {
        return $this->belongsTo('Crater\Models\User', 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo('Crater\Models\User', 'creator_id');
    }

    public function company()
    {
        return $this->belongsTo('Crater\Models\Company');
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class);
    }

    public function getEstimateNumAttribute()
    {
        $position = $this->strposX($this->estimate_number, "-", 1) + 1;

        return substr($this->estimate_number, $position);
    }

    public function getEstimatePrefixAttribute()
    {
        $prefix = explode("-", $this->estimate_number)[0];

        return $prefix;
    }

    private function strposX($haystack, $needle, $number)
    {
        if ($number == '1') {
            return strpos($haystack, $needle);
        } elseif ($number > '1') {
            return strpos(
                $haystack,
                $needle,
                $this->strposX($haystack, $needle, $number - 1) + strlen($needle)
            );
        } else {
            return error_log('Error: Value for parameter $number is out of range');
        }
    }

    public function getFormattedExpiryDateAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->expiry_date)->format($dateFormat);
    }

    public function getFormattedEstimateDateAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->estimate_date)->format($dateFormat);
    }

    public function scopeEstimatesBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'estimates.estimate_date',
            [$start->format('Y-m-d'), $end->format('Y-m-d')]
        );
    }

    public function scopeWhereStatus($query, $status)
    {
        return $query->where('estimates.status', $status);
    }

    public function scopeWhereEstimateNumber($query, $estimateNumber)
    {
        return $query->where('estimates.estimate_number', $estimateNumber);
    }

    public function scopeWhereEstimate($query, $estimate_id)
    {
        $query->orWhere('id', $estimate_id);
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->whereHas('user', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%')
                    ->orWhere('contact_name', 'LIKE', '%'.$term.'%')
                    ->orWhere('company_name', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('estimate_number')) {
            $query->whereEstimateNumber($filters->get('estimate_number'));
        }

        if ($filters->get('status')) {
            $query->whereStatus($filters->get('status'));
        }

        if ($filters->get('estimate_id')) {
            $query->whereEstimate($filters->get('estimate_id'));
        }

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->estimatesBetween($start, $end);
        }

        if ($filters->get('customer_id')) {
            $query->whereCustomer($filters->get('customer_id'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'estimate_number';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('estimates.company_id', $company_id);
    }

    public function scopeWhereCustomer($query, $customer_id)
    {
        $query->where('estimates.user_id', $customer_id);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public static function createEstimate($request)
    {
        $data = $request->except(['items', 'taxes']);

        $data['creator_id'] = Auth::id();
        $data['status'] = self::STATUS_DRAFT;
        $data['unique_hash'] = str_random(60);
        $data['company_id'] = $request->header('company');

        $data['tax_per_item'] = CompanySetting::getSetting(
            'tax_per_item',
            $request->header('company')
        ) ?? 'NO';

        $data['discount_per_item'] = CompanySetting::getSetting(
            'discount_per_item',
            $request->header('company')
        ) ?? 'NO';

        if ($request->has('estimateSend')) {
            $data['status'] = self::STATUS_SENT;
        }

        $estimate = self::create($data);
        $estimate->unique_hash = Hashids::connection(Estimate::class)->encode($estimate->id);
        $estimate->save();

        self::createItems($estimate, $request);

        if ($request->has('taxes') && (! empty($request->taxes))) {
            self::createTaxes($estimate, $request);
        }

        $customFields = $request->customFields;

        if ($customFields) {
            $estimate->addCustomFields($customFields);
        }

        return Estimate::with([
            'items.taxes',
            'user',
            'taxes'
        ])
            ->find($estimate->id);
    }

    public function updateEstimate($request)
    {
        $data = $request->except(['items', 'taxes']);

        $this->update($data);

        $this->items()->delete();
        $this->taxes()->delete();

        self::createItems($this, $request);

        if ($request->has('taxes') && (! empty($request->taxes))) {
            self::createTaxes($this, $request);
        }

        if ($request->customFields) {
            $this->updateCustomFields($request->customFields);
        }

        return Estimate::with([
                'items.taxes',
                'user',
                'taxes'
            ])
            ->find($this->id);
    }

    public static function createItems($estimate, $request)
    {
        $estimateItems = $request->items;

        foreach ($estimateItems as $estimateItem) {
            $estimateItem['company_id'] = $request->header('company');
            $item = $estimate->items()->create($estimateItem);

            if (array_key_exists('taxes', $estimateItem) && $estimateItem['taxes']) {
                foreach ($estimateItem['taxes'] as $tax) {
                    if (gettype($tax['amount']) !== "NULL") {
                        $tax['company_id'] = $request->header('company');
                        $item->taxes()->create($tax);
                    }
                }
            }
        }
    }

    public static function createTaxes($estimate, $request)
    {
        $estimateTaxes = $request->taxes;

        foreach ($estimateTaxes as $tax) {
            if (gettype($tax['amount']) !== "NULL") {
                $tax['company_id'] = $request->header('company');
                $estimate->taxes()->create($tax);
            }
        }
    }

    public function send($data)
    {
        $data['estimate'] = $this->toArray();
        $data['user'] = $this->user->toArray();
        $data['company'] = $this->company->toArray();
        $data['body'] = $this->getEmailBody($data['body']);
        $data['attach']['data'] = ($this->getEmailAttachmentSetting()) ? $this->getPDFData() : null;

        \Mail::to($data['to'])->send(new SendEstimateMail($data));

        if ($this->status == Estimate::STATUS_DRAFT) {
            $this->status = Estimate::STATUS_SENT;
            $this->save();
        }

        return [
            'success' => true,
        ];
    }

    public function getPDFData()
    {
        $taxes = collect();

        if ($this->tax_per_item === 'YES') {
            foreach ($this->items as $item) {
                foreach ($item->taxes as $tax) {
                    $found = $taxes->filter(function ($item) use ($tax) {
                        return $item->tax_type_id == $tax->tax_type_id;
                    })->first();

                    if ($found) {
                        $found->amount += $tax->amount;
                    } else {
                        $taxes->push($tax);
                    }
                }
            }
        }

        $estimateTemplate = self::find($this->id)->template_name;

        $company = Company::find($this->company_id);
        $locale = CompanySetting::getSetting('language', $company->id);

        App::setLocale($locale);

        $logo = $company->logo_path;

        view()->share([
            'estimate' => $this,
            'logo' => $logo ?? null,
            'company_address' => $this->getCompanyAddress(),
            'shipping_address' => $this->getCustomerShippingAddress(),
            'billing_address' => $this->getCustomerBillingAddress(),
            'notes' => $this->getNotes(),
            'taxes' => $taxes,
        ]);

        return PDF::loadView('app.pdf.estimate.'.$estimateTemplate);
    }

    public function getCompanyAddress()
    {
        if ($this->company && (! $this->company->address()->exists())) {
            return false;
        }

        $format = CompanySetting::getSetting('estimate_company_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getCustomerShippingAddress()
    {
        if ($this->user && (! $this->user->shippingAddress()->exists())) {
            return false;
        }

        $format = CompanySetting::getSetting('estimate_shipping_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getCustomerBillingAddress()
    {
        if ($this->user && (! $this->user->billingAddress()->exists())) {
            return false;
        }

        $format = CompanySetting::getSetting('estimate_billing_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getNotes()
    {
        return $this->getFormattedString($this->notes);
    }

    public function getEmailAttachmentSetting()
    {
        $estimateAsAttachment = CompanySetting::getSetting('estimate_email_attachment', $this->company_id);

        if ($estimateAsAttachment == 'NO') {
            return false;
        }

        return true;
    }

    public function getEmailBody($body)
    {
        $values = array_merge($this->getFieldsArray(), $this->getExtraFields());

        $body = strtr($body, $values);

        return preg_replace('/{(.*?)}/', '', $body);
    }

    public function getExtraFields()
    {
        return [
            '{ESTIMATE_DATE}' => $this->formattedEstimateDate,
            '{ESTIMATE_EXPIRY_DATE}' => $this->formattedExpiryDate,
            '{ESTIMATE_NUMBER}' => $this->estimate_number,
            '{ESTIMATE_REF_NUMBER}' => $this->reference_number,
            '{ESTIMATE_LINK}' => url('/customer/estimates/pdf/'.$this->unique_hash),
        ];
    }
}
