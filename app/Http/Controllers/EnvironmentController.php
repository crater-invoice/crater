<?php

namespace Crater\Http\Controllers;

use Exception;
use Validator;
use Crater\Setting;
use Illuminate\Http\Request;
use Crater\Space\EnvironmentManager;
use Crater\Http\Requests\DatabaseEnvironmentRequest;
use Crater\Http\Requests\MailEnvironmentRequest;
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
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        $results = $this->EnvironmentManager->saveDatabaseVariables($request);

        if(array_key_exists("success", $results)) {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('storage:link');
            Artisan::call('key:generate --force');
            Artisan::call('migrate --seed --force');
            Artisan::call('migrate', ['--path' => 'vendor/laravel/passport/database/migrations', '--force' => true]);

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
            'mail_encryption' => config('mail.encryption'),
            'from_name' => config('mail.from.name'),
            'from_mail' => config('mail.from.address'),
            'mail_mailgun_endpoint' => config('services.mailgun.endpoint'),
            'mail_mailgun_domain' => config('services.mailgun.domain'),
            'mail_mailgun_secret' => config('services.mailgun.secret'),
            'mail_ses_key' => config('services.ses.key'),
            'mail_ses_secret' => config('services.ses.secret'),
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
            'ses'
        ];

        return response()->json($drivers);
    }
}
