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
        $results = $this->EnvironmentManager->saveMailVariables($request);

        Setting::setSetting('profile_complete', 4);

        return response()->json($results);
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
