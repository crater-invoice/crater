<?php
namespace Crater\Http\Controllers;

use Illuminate\Http\Request;
use Crater\User;
use Crater\Company;
use Crater\Address;
use Crater\Http\Requests\ProfileRequest;
use Crater\Http\Requests\CompanyRequest;
use Crater\Http\Requests\CompanySettingRequest;
use Crater\Space\DateFormatter;
use Crater\Space\TimeZones;
use Crater\Currency;
use Crater\Setting;
use Crater\CompanySetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class OnboardingController extends Controller
{

    /**
     * Retrieve Onboarding data.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOnboardingData(Request $request)
    {
        if (!\Storage::disk('local')->has('database_created')) {
            return response()->json([
                'profile_complete' => '0'
            ]);
        }

        $setting = Setting::getSetting('profile_complete');

        if ($setting !== 'COMPLETED' && $setting < 4){
            return response()->json([
                'profile_complete' => $setting
            ]);
        }

        $date_formats = DateFormatter::get_list();
        $time_zones = TimeZones::get_list();
        $languages = [
            ["code"=>"ar", "name" => "Arabic"],
            ["code"=>"en", "name" => "English"],
            ["code"=>"fr", "name" => "French"],
            ["code"=>"es", "name" => "Spanish"],
            ["code"=>"ar", "name" => "العربية"],
            ["code"=>"de", "name" => "German"],
            ["code"=>"pt-br", "name" => "Portuguese (Brazilian)"],
            ["code"=>"it", "name" => "Italian"],
        ];
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
        $user = User::with([
            'addresses',
            'addresses.country',
            'company'
        ])->find(1);

        return response()->json([
            'user' => $user,
            'profile_complete' => $setting,
            'languages' => $languages,
            'date_formats' => $date_formats,
            'time_zones' => $time_zones,
            'fiscal_years' => $fiscal_years,
            'currencies' => Currency::all()
        ]);
    }


    /**
     * Setup Admin Profile.
     *
     * @param  \Crater\Http\Requests\ProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminProfile(ProfileRequest $request)
    {
        $setting = Setting::getSetting('profile_complete');

        if ($setting == '1' || $setting == 'COMPLETED') {
            return response()->json(['error' => 'Profile already created.']);
        } else {
            Setting::setSetting('profile_complete', 5);
        }

        $user = User::find(1);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Setup Admin Avatar.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAdminAvatar(Request $request)
    {
        $setting = Setting::getSetting('profile_complete');

        if ($setting == '1' || $setting == 'COMPLETED') {
            return response()->json(['error' => 'Profile already created.']);
        }
        $data = json_decode($request->admin_avatar);

        if($data) {
            $user = User::find($data->id);
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

    /**
     * Setup Admin Company.
     *
     * @param  \Crater\Http\Requests\CompanyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminCompany(CompanyRequest $request)
    {
        $setting = Setting::getSetting('profile_complete');

        if ($setting == '6' || $setting == 'COMPLETED') {
            return response()->json(['error' => 'Company already created.']);
        } else {
            Setting::setSetting('profile_complete', 6);
        }

        $user = User::find(1);
        $company = $user->company;

        if (!$company) {
            $company = new Company();
        }

        $company->name = $request->name;
        $company->unique_hash = str_random(60);
        $company->save();
        $user->company()->associate($company);
        $user->save();

        if ($request->has('logo') && $request->logo !== null && $request->logo !== 'undefined' ) {
            $company->addMediaFromRequest('logo')->toMediaCollection('logo');

        }

        $fields = $request->only([
            'address_street_1',
            'address_street_2',
            'city',
            'state',
            'country_id',
            'zip',
            'phone'
        ]);
        $address = Address::updateOrCreate(['user_id' => 1], $fields);
        $user = User::with('addresses', 'company')->find(1);

        CompanySetting::setSetting(
            'notification_email',
            $user->email,
            $company->id
        );

        return response()->json([
            'user' => $user
        ]);
    }


    /**
     * Setup Company Settings.
     *
     * @param  \Crater\Http\Requests\CompanySettingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function companySettings(CompanySettingRequest $request)
    {
        $setting = Setting::getSetting('profile_complete');

        if($setting == 'COMPLETED') {
            return response()->json(['error' => 'Settings already saved.']);
        } else {
            Setting::setSetting('profile_complete', 'COMPLETED');
        }

        $user = User::find(1);

        $sets = ['currency',
            'time_zone',
            'language',
            'carbon_date_format',
            'moment_date_format',
            'fiscal_year'
        ];

        foreach ($sets as $key) {
            CompanySetting::setSetting(
                $key,
                $request->$key,
                $user->company_id
            );
        }

        $invoices = [
            'invoice_auto_generate' => 'YES',
            'invoice_prefix' => 'INV'
        ];

        foreach ($invoices as $key => $value) {
            CompanySetting::setSetting(
                $key,
                $value,
                $user->company_id
            );
        }

        $estimates = [
            'estimate_prefix' => 'EST',
            'estimate_auto_generate' => 'YES'
        ];

        foreach ($estimates as $key => $value) {
            CompanySetting::setSetting(
                $key,
                $value,
                $user->company_id
            );
        }

        $payments = [
            'payment_prefix' => 'PAY',
            'payment_auto_generate' => 'YES'
        ];

        foreach ($payments as $key => $value) {
            CompanySetting::setSetting(
                $key,
                $value,
                $user->company_id
            );
        }

        $colors = [
            'primary_text_color' => '#5851D8',
            'heading_text_color' => '#595959',
            'section_heading_text_color' => '#040405',
            'border_color' => '#EAF1FB',
            'body_text_color' => '#595959',
            'footer_text_color' => '#595959',
            'footer_total_color' => '#5851D8',
            'footer_bg_color' => '#F9FBFF',
            'date_text_color' => '#A5ACC1',
            'invoice_primary_color' => '#5851D8',
            'invoice_column_heading' => '#55547A',
            'invoice_field_label' => '#55547A',
            'invoice_field_value' => '#040405',
            'invoice_body_text' => '#040405',
            'invoice_description_text' => '#595959',
            'invoice_border_color' => '#EAF1FB'
        ];
        foreach ($colors as $key => $value) {
            CompanySetting::setSetting(
                $key,
                $value,
                $user->company_id
            );
        }

        Setting::setSetting('version', config('crater.version'));

        Artisan::call('passport:install --force');

        Artisan::call('db:seed', ['--class' => 'PaymentMethodSeeder', '--force' => true]);

        Artisan::call('db:seed', ['--class' => 'UnitSeeder', '--force' => true]);

        $client = DB::table('oauth_clients')->find(2);

        $path = base_path('.env');

        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                'PROXY_OAUTH_CLIENT_SECRET='.config('auth.proxy.client_secret'),
                'PROXY_OAUTH_CLIENT_SECRET='.$client->secret,
                file_get_contents($path)
            ));
            file_put_contents($path, str_replace(
                'APP_DEBUG=true',
                'APP_DEBUG=false',
                file_get_contents($path)
            ));
        }

        $data['token'] = $user->createToken('password')->accessToken;

        return response()->json($data);
    }
}
