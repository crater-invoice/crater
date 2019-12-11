<?php
namespace Crater\Http\Controllers;

use Illuminate\Http\Request;
use Crater\User;
use Crater\Setting;
use Crater\Company;
use Crater\Address;
use Crater\Http\Requests\SettingRequest;
use Crater\Http\Requests\SettingKeyRequest;
use Crater\Http\Requests\ProfileRequest;
use Crater\Http\Requests\CompanyRequest;
use Crater\Http\Requests\CompanySettingRequest;
use Crater\Http\Requests\NotificationSettingsRequest;
use Crater\Space\CurrencyFormatter;
use Crater\Space\DateFormatter;
use Crater\Space\TimeZones;
use Crater\Currency;
use Crater\CompanySetting;

class CompanyController extends Controller
{
    public function getAdmin()
    {
        return User::find(1);
    }

    public function updateAdminProfile(ProfileRequest $request)
    {
        $verifyEmail = User::where('email', $request->email)->first();

        $user = auth()->user();

        if ($verifyEmail) {
            if ($verifyEmail->id !== $user->id) {
                return response()->json([
                    'error' => 'Email already in use'
                ]);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json([
            'user' => $user,
            'success' => true
        ]);
    }

    public function getAdminCompany()
    {
        $user = User::with(['addresses', 'addresses.country', 'company'])->find(1);

        return response()->json([
            'user' => $user
        ]);
    }

    public function updateAdminCompany(CompanyRequest $request)
    {
        $user = User::find(1);
        $company = $user->company;
        $company->name = $request->name;
        $company->save();

        if ($request->has('logo')) {
            $company->clearMediaCollection('logo');
            $company->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

        $fields = $request->only(['address_street_1', 'address_street_2', 'city', 'state', 'country_id', 'zip', 'phone']);
        $address = Address::updateOrCreate(['user_id' => 1], $fields);
        $user = User::with(['addresses', 'addresses.country', 'company'])->find(1);

        return response()->json([
            'user' => $user,
            'success' => true
        ]);
    }

    public function getGeneralSettings(Request $request)
    {
        $date_formats = DateFormatter::get_list();

        $time_zones = TimeZones::get_list();
        $fiscal_years = [
            ['key' => 'january-december' , 'value' => '1-12'],
            ['key' => 'february-january' , 'value' => '2-1'],
            ['key' => 'march-february'   , 'value' => '3-2'],
            ['key' => 'april-march'      , 'value' => '4-3'],
            ['key' => 'may-april'        , 'value' => '5-4'],
            ['key' => 'june-may'         , 'value' => '6-5'],
            ['key' => 'july-june'        , 'value' => '7-6'],
            ['key' => 'august-july'      , 'value' => '8-7'],
            ['key' => 'september-august' , 'value' => '9-8'],
            ['key' => 'october-september', 'value' => '10-9'],
            ['key' => 'november-october' , 'value' => '11-10'],
            ['key' => 'december-november', 'value' => '12-11'],
        ];

        $language = CompanySetting::getSetting('language', $request->header('company'));
        $carbon_date_format = CompanySetting::getSetting('carbon_date_format', $request->header('company'));
        $moment_date_format = CompanySetting::getSetting('moment_date_format', $request->header('company'));
        $time_zone = CompanySetting::getSetting('time_zone', $request->header('company'));
        $currency = CompanySetting::getSetting('currency', $request->header('company'));
        $fiscal_year = CompanySetting::getSetting('fiscal_year', $request->header('company'));

        $languages = [
            ["code"=>"en", "name" => "English"],
            ["code"=>"fr", "name" => "French"],
            ["code"=>"es", "name" => "Spanish"]
        ];

        return response()->json([
            'languages' => $languages,
            'date_formats' => $date_formats,
            'time_zones' => $time_zones,
            'time_zone' => $time_zone,
            'currencies' => Currency::all(),
            'fiscal_years' => $fiscal_years,
            'fiscal_year' => $fiscal_year,
            'selectedLanguage' => $language,
            'selectedCurrency' => $currency,
            'carbon_date_format' => $carbon_date_format,
            'moment_date_format' => $moment_date_format,
        ]);
    }

    public function updateGeneralSettings(CompanySettingRequest $request)
    {
        $sets = [
            'currency',
            'time_zone',
            'language',
            'carbon_date_format',
            'fiscal_year',
            'moment_date_format'
        ];

        foreach ($sets as $key) {
            CompanySetting::setSetting($key, $request->$key, $request->header('company'));
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function getCustomizeSetting (Request $request)
    {
        $invoice_prefix = CompanySetting::getSetting('invoice_prefix', $request->header('company'));
        $invoice_auto_generate = CompanySetting::getSetting('invoice_auto_generate', $request->header('company'));
        $invoice_notes = CompanySetting::getSetting('invoice_notes',  $request->header('company'), true);
        $invoice_terms_and_conditions = CompanySetting::getSetting('invoice_terms_and_conditions', $request->header('company'), true);

        $estimate_prefix = CompanySetting::getSetting('estimate_prefix', $request->header('company'));
        $estimate_notes = CompanySetting::getSetting('estimate_notes', $request->header('company'), true);
        $estimate_terms_and_conditions = CompanySetting::getSetting('estimate_terms_and_conditions', $request->header('company'), true);
        $estimate_auto_generate  = CompanySetting::getSetting('estimate_auto_generate', $request->header('company'));

        $payment_prefix = CompanySetting::getSetting('payment_prefix', $request->header('company'));
        $payment_auto_generate = CompanySetting::getSetting('payment_auto_generate', $request->header('company'));

        $billing_address_format = CompanySetting::getSetting('billing_address_format', $request->header('company'), $large= true);
        $shipping_address_format = CompanySetting::getSetting('shipping_address_format', $request->header('company'), $large= true);
        $company_address_format = CompanySetting::getSetting('company_address_format', $request->header('company'), $large= true);

        return  response()->json([
            'invoice_prefix' => $invoice_prefix,
            'invoice_auto_generate' => $invoice_auto_generate,
            'invoice_notes' => $invoice_notes,
            'invoice_terms_and_conditions' => $invoice_terms_and_conditions,
            'estimate_prefix' => $estimate_prefix,
            'estimate_auto_generate' => $estimate_auto_generate,
            'estimate_notes' => $estimate_notes,
            'estimate_terms_and_conditions' => $estimate_terms_and_conditions,
            'payment_prefix' => $payment_prefix,
            'payment_auto_generate' => $payment_auto_generate,
            'billing_address_format' => $billing_address_format,
            'shipping_address_format' => $shipping_address_format,
            'company_address_format' => $company_address_format
        ]);
    }

    public function updateCustomizeSetting (Request $request)
    {
        $sets = [];

        if ($request->type == "PAYMENTS") {
            $sets = [
                'payment_prefix'
            ];
        } elseif ($request->type == "INVOICES") {
            $sets = [
                'invoice_prefix',
                'invoice_notes',
                'invoice_terms_and_conditions'
            ];
        } elseif ($request->type == "ESTIMATES") {
            $sets = [
                'estimate_prefix',
                'estimate_notes',
                'estimate_terms_and_conditions'
            ];
        } else {
            $sets = [
                'billing_address_format',
                'shipping_address_format',
                'company_address_format'
            ];
        }

        foreach ($sets as $key) {
            if ($key == 'invoice_terms_and_conditions' || $key == 'invoice_notes' || $request->type == "ADDRESSES" || $key === 'estimate_terms_and_conditions' || $key === 'estimate_notes') {
                CompanySetting::setSetting($key, $request->$key, $request->header('company'), true);
            } else {
                CompanySetting::setSetting($key, $request->$key, $request->header('company'));
            }
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function updateSetting(SettingRequest $request)
    {
        CompanySetting::setSetting($request->key, $request->value, $request->header('company'));

        return response()->json([
            'success' => true
        ]);
    }

    public function getSetting(SettingKeyRequest $request)
    {
        $setting = CompanySetting::getSetting($request->key, $request->header('company'));

        return response()->json([
            $request->key => $setting
        ]);
    }

    public function getColors(Request $request)
    {
        $colors = [
            'invoice_primary_color',
            'invoice_column_heading',
            'invoice_field_label',
            'invoice_field_value',
            'invoice_body_text',
            'invoice_description_text',
            'invoice_border_color',
            'primary_text_color',
            'heading_text_color',
            'section_heading_text_color',
            'border_color',
            'body_text_color',
            'footer_text_color',
            'footer_total_color',
            'footer_bg_color',
            'date_text_color'
        ];

        $colorSettings = CompanySetting::whereIn('option', $colors)
            ->whereCompany($request->header('company'))
            ->get();

        return response()->json([
            'colorSettings' => $colorSettings
        ]);
    }

    /**
     * Upload the company logo to storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function uploadCompanyLogo(Request $request)
    {
        $data = json_decode($request->company_logo);

        if($data) {
            $company = Company::find($request->header('company'));

            if($company) {
                $company->clearMediaCollection('logo');

                $company->addMediaFromBase64($data->data)
                    ->usingFileName($data->name)
                    ->toMediaCollection('logo');
            }
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Upload the Admin Avatar to public storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function uploadAdminAvatar(Request $request)
    {
        $data = json_decode($request->admin_avatar);

        if($data) {
            $user = auth()->user();

            if($user) {
                $user->clearMediaCollection('admin_avatar');

                $user->addMediaFromBase64($data->data)
                    ->usingFileName($data->name)
                    ->toMediaCollection('admin_avatar');
            }
        }

        return response()->json([
            'user' => $user,
            'success' => true
        ]);
    }
}
