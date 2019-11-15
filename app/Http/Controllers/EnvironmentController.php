<?php

namespace Laraspace\Http\Controllers;

use Exception;
use Validator;
use Laraspace\Setting;
use Illuminate\Http\Request;
use Laraspace\Space\EnvironmentManager;
use Laraspace\Http\Requests\DatabaseEnvironmentRequest;
use Laraspace\Http\Requests\MailEnvironmentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

class EnvironmentController extends Controller
{
    /**
     * @var EnvironmentManager
     */
    protected $EnvironmentManager;

    /**
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->EnvironmentManager = $environmentManager;
    }

    /**
     *
     * @param DatabaseEnvironmentRequest $request
     */
    public function saveDatabaseEnvironment(DatabaseEnvironmentRequest $request)
    {
        $results = $this->EnvironmentManager->saveDatabaseVariables($request);


        if(array_key_exists("success", $results)) {

            Artisan::call('config:clear');
            Artisan::call('key:generate --force');
            Artisan::call('migrate --seed');
            Artisan::call('migrate', ['--path' => 'vendor/laravel/passport/database/migrations']);

            \Storage::disk('local')->put('database_created', 'database_created');

            Setting::setSetting('profile_complete', 3);
        }

        return response()->json($results);
    }

    /**
     *
     * @param DatabaseEnvironmentRequest $request
     * @return JsonResponse
     */
    public function saveMailEnvironment(MailEnvironmentRequest $request)
    {
        $setting = Setting::getSetting('profile_complete');
        $results = $this->EnvironmentManager->saveMailVariables($request);

        if ($setting !== 'COMPLETED')
        {
            Setting::setSetting('profile_complete', 4);
        }

        return response()->json($results);
    }

    public function getMailEnvironment()
    {
        $MailData = [
            'mail_driver' => config('mail.driver'),
            'mail_host' => config('mail.host'),
            'mail_port' => config('mail.port'),
            'mail_username' => config('mail.username'),
            'mail_password' => config('mail.password'),
            'mail_encryption' => config('mail.encryption')
        ];

        return response()->json($MailData);
    }

    /**
     *
     * @return JsonResponse
     */
    public function getMailDrivers()
    {
        $drivers = [
            'smtp',
            'mail',
            'sendmail',
            'mailgun',
            'mandrill',
            'ses',
            'sparkpost'
        ];

        return response()->json($drivers);
    }
}
