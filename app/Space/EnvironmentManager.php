<?php

namespace Crater\Space;

use Exception;
use Illuminate\Http\Request;
use Crater\Http\Requests\DatabaseEnvironmentRequest;
use Crater\Http\Requests\MailEnvironmentRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class EnvironmentManager
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
    }

    /**
     * Save the form content to the .env file.
     *
     * @param DatabaseEnvironmentRequest $request
     * @return array
     */
    public function saveDatabaseVariables(DatabaseEnvironmentRequest $request)
    {
        $oldDatabaseData =
        'DB_CONNECTION='.config('database.default')."\n".
        'DB_HOST='.config('database.connections.'.config('database.default').'.host')."\n".
        'DB_PORT='.config('database.connections.'.config('database.default').'.port')."\n".
        'DB_DATABASE='.config('database.connections.'.config('database.default').'.database')."\n".
        'DB_USERNAME='.config('database.connections.'.config('database.default').'.username')."\n".
        'DB_PASSWORD='.config('database.connections.'.config('database.default').'.password')."\n\n";

        $newDatabaseData =
        'DB_CONNECTION='.$request->database_connection."\n".
        'DB_HOST='.$request->database_hostname."\n".
        'DB_PORT='.$request->database_port."\n".
        'DB_DATABASE='.$request->database_name."\n".
        'DB_USERNAME='.$request->database_username."\n".
        'DB_PASSWORD='.$request->database_password."\n\n";

        if (! $this->checkDatabaseConnection($request)) {

            return [
                'error' => 'connection_failed'
            ];
        } else {
            if(count(DB::connection()->select('SHOW TABLES'))) {
                return [
                    'error' => 'database_should_be_empty'
                ];
            }
        }

        try {

            file_put_contents($this->envPath, str_replace(
                $oldDatabaseData,
                $newDatabaseData,
                file_get_contents($this->envPath)
            ));

            file_put_contents($this->envPath, str_replace(
                'APP_URL='.config('app.url'),
                'APP_URL='.$request->app_url,
                file_get_contents($this->envPath)
            ));

        } catch (Exception $e) {
            return [
                'error' => 'database_variables_save_error'
            ];
        }

        return [
            'success' => 'database_variables_save_successfully'
        ];
    }

    /**
     * Save the form content to the .env file.
     *
     * @param Request $request
     * @return array
     */
    public function saveMailVariables(MailEnvironmentRequest $request)
    {
        $oldMailData =
        'MAIL_DRIVER='.config('mail.driver')."\n".
        'MAIL_HOST='.config('mail.host')."\n".
        'MAIL_PORT='.config('mail.port')."\n".
        'MAIL_USERNAME='.config('mail.username')."\n".
        'MAIL_PASSWORD='.config('mail.password')."\n".
        'MAIL_ENCRYPTION='.config('mail.encryption')."\n\n";

        $newMailData =
        'MAIL_DRIVER='.$request->mail_driver."\n".
        'MAIL_HOST='.$request->mail_host."\n".
        'MAIL_PORT='.$request->mail_port."\n".
        'MAIL_USERNAME='.$request->mail_username."\n".
        'MAIL_PASSWORD='.$request->mail_password."\n".
        'MAIL_ENCRYPTION='.$request->mail_encryption."\n\n";

        try {

            file_put_contents($this->envPath, str_replace(
                $oldMailData,
                $newMailData,
                file_get_contents($this->envPath)
            ));

            file_put_contents($this->envPath, str_replace(
                $oldMailData,
                $newMailData,
                file_get_contents($this->envPath)
            ));

        } catch (Exception $e) {
            return [
                'error' => 'mail_variables_save_error'
            ];
        }

        return [
            'success' => 'mail_variables_save_successfully'
        ];
    }

    /**
     *
     * @param DatabaseEnvironmentRequest $request
     * @return bool
     */
    private function checkDatabaseConnection(DatabaseEnvironmentRequest $request)
    {
        $connection = $request->database_connection;

        $settings = config("database.connections.$connection");

        config([
            'database' => [
                'migrations' => 'migrations',
                'default' => $connection,
                'connections' => [
                    $connection => array_merge($settings, [
                        'driver'   => $connection,
                        'host'     => $request->database_hostname,
                        'port'     => $request->database_port,
                        'database' => $request->database_name,
                        'username' => $request->database_username,
                        'password' => $request->database_password,
                    ]),
                ],
            ],
        ]);

        try {
            DB::connection()->getPdo();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
