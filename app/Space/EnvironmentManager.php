<?php

namespace InvoiceShelf\Space;

use Exception;
use Illuminate\Support\Facades\DB;
use InvoiceShelf\Http\Requests\DatabaseEnvironmentRequest;
use InvoiceShelf\Http\Requests\DiskEnvironmentRequest;
use InvoiceShelf\Http\Requests\DomainEnvironmentRequest;
use InvoiceShelf\Http\Requests\MailEnvironmentRequest;

class EnvironmentManager
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * @var string
     */
    private $delimiter = "\n";

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
    }

    /**
     * Returns the .env contents
     * @return false|string
     */
    private function getEnvContents()
    {
        return file_get_contents($this->envPath);
    }

    /**
     * Updates .env file - inspired by Akaunting
     *
     * @return bool
     */
    public function updateEnv(array $data)
    {
        if (empty($data) || ! is_array($data) || ! is_file($this->envPath)) {
            return false;
        }

        $env = $this->getEnvContents();

        $env = explode($this->delimiter, $env);

        foreach ($data as $data_key => $data_value) {
            $updated = false;

            foreach ($env as $env_key => $env_value) {
                $entry = explode('=', $env_value, 2);

                // Check if new or old key
                if ($entry[0] == $data_key) {
                    $env[$env_key] = $data_key.'='.$data_value;
                    $updated = true;
                } else {
                    $env[$env_key] = $env_value;
                }
            }

            // Lets create if not available
            if (! $updated) {
                $env[] = $data_key.'='.$data_value;
            }
        }

        $env = implode($this->delimiter, $env);

        file_put_contents(base_path('.env'), $env);

        return true;
    }

    /**
     * Save the database content to the .env file.
     *
     * @return array
     */
    public function saveDatabaseVariables(DatabaseEnvironmentRequest $request)
    {

        $dbEnv = [
            'APP_URL' => $request->get('app_url'),
            'DB_CONNECTION' => $request->get('database_connection'),
            'SANCTUM_STATEFUL_DOMAINS' => $request->get('app_domain'),
            'SESSION_DOMAIN' => explode(':', $request->get('app_domain'))[0],
        ];

        if ($request->has('database_username') && $request->has('database_password')) {
            $dbEnv['DB_HOST'] = $request->get('database_hostname');
            $dbEnv['DB_PORT'] = $request->get('database_port');
            $dbEnv['DB_DATABASE'] = $request->get('database_name');
            $dbEnv['DB_USERNAME'] = $request->get('database_username');
            $dbEnv['DB_PASSWORD'] = $request->get('database_password');

        } else {
            $dbEnv['DB_DATABASE'] = $request->get('database_name');
        }

        try {
            $this->checkDatabaseConnection($request);

            if (\Schema::hasTable('users')) {
                return [
                    'error' => 'database_should_be_empty',
                ];
            }
        } catch (Exception $e) {
            return [
                'error_message' => $e->getMessage(),
            ];
        }

        try {
            $this->updateEnv($dbEnv);
        } catch (Exception $e) {
            return [
                'error' => 'database_variables_save_error',
            ];
        }

        return [
            'success' => 'database_variables_save_successfully',
        ];
    }

    /**
     * Returns PDO object if all ok.
     *
     * @return \Closure|\PDO
     */
    private function checkDatabaseConnection(DatabaseEnvironmentRequest $request)
    {
        $connection = $request->get('database_connection');

        $settings = config("database.connections.$connection");

        $connectionArray = array_merge($settings, [
            'driver' => $connection,
            'database' => $request->get('database_name'),
        ]);

        if ($request->has('database_username') && $request->has('database_password')) {
            $connectionArray = array_merge($connectionArray, [
                'username' => $request->get('database_username'),
                'password' => $request->get('database_password'),
                'host' => $request->get('database_hostname'),
                'port' => $request->get('database_port'),
            ]);
        }

        config([
            'database' => [
                'migrations' => 'migrations',
                'default' => $connection,
                'connections' => [$connection => $connectionArray],
            ],
        ]);

        return DB::connection()->getPdo();
    }

    /**
     * Save the mail content to the .env file.
     *
     * @return array
     */
    public function saveMailVariables(MailEnvironmentRequest $request)
    {
        $mailEnv = $this->getMailConfiguration($request);

        try {

            $this->updateEnv($mailEnv);

        } catch (Exception $e) {
            return [
                'error' => 'mail_variables_save_error',
            ];
        }

        return [
            'success' => 'mail_variables_save_successfully',
        ];
    }

    /**
     * Returns the mail configuration
     *
     * @param  MailEnvironmentRequest  $request
     * @return array
     */
    private function getMailConfiguration($request)
    {
        $mailEnv = [];

        $driver = $request->get('mail_driver');

        switch ($driver) {
            case 'smtp':

                $mailEnv = [
                    'MAIL_DRIVER' => $request->get('mail_driver'),
                    'MAIL_HOST' => $request->get('mail_host'),
                    'MAIL_PORT' => $request->get('mail_port'),
                    'MAIL_USERNAME' => $request->get('mail_username'),
                    'MAIL_PASSWORD' => $request->get('mail_password'),
                    'MAIL_ENCRYPTION' => $request->get('mail_encryption'),
                    'MAIL_FROM_ADDRESS' => $request->get('from_mail'),
                    'MAIL_FROM_NAME' => $request->get('from_name'),
                ];

                break;

            case 'mailgun':

                $mailEnv = [
                    'MAIL_DRIVER' => $request->get('mail_driver'),
                    'MAIL_HOST' => $request->get('mail_host'),
                    'MAIL_PORT' => $request->get('mail_port'),
                    'MAIL_USERNAME' => config('mail.username'),
                    'MAIL_PASSWORD' => config('mail.password'),
                    'MAIL_ENCRYPTION' => $request->get('mail_encryption'),
                    'MAIL_FROM_ADDRESS' => $request->get('from_mail'),
                    'MAIL_FROM_NAME' => $request->get('from_name'),
                    'MAILGUN_DOMAIN' => $request->get('mail_mailgun_domain'),
                    'MAILGUN_SECRET' => $request->get('mail_mailgun_secret'),
                    'MAILGUN_ENDPOINT' => $request->get('mail_mailgun_endpoint'),
                ];

                break;

            case 'ses':

                $mailEnv = [
                    'MAIL_DRIVER' => $request->get('mail_driver'),
                    'MAIL_HOST' => $request->get('mail_host'),
                    'MAIL_PORT' => $request->get('mail_port'),
                    'MAIL_USERNAME' => config('mail.username'),
                    'MAIL_PASSWORD' => config('mail.password'),
                    'MAIL_ENCRYPTION' => $request->get('mail_encryption'),
                    'MAIL_FROM_ADDRESS' => $request->get('from_mail'),
                    'MAIL_FROM_NAME' => $request->get('from_mail'),
                    'SES_KEY' => $request->get('from_mail'),
                    'SES_SECRET' => $request->get('from_name'),
                ];

                break;

            case 'sendmail':
            case 'mail':

                $mailEnv = [
                    'MAIL_DRIVER' => $request->get('mail_driver'),
                    'MAIL_HOST' => config('mail.host'),
                    'MAIL_PORT' => config('mail.port'),
                    'MAIL_USERNAME' => config('mail.username'),
                    'MAIL_PASSWORD' => config('mail.password'),
                    'MAIL_ENCRYPTION' => config('mail.encryption'),
                    'MAIL_FROM_ADDRESS' => $request->get('from_mail'),
                    'MAIL_FROM_NAME' => $request->get('from_name'),
                ];

                break;

        }

        return $mailEnv;
    }

    /**
     * Save the disk content to the .env file.
     *
     * @return array
     */
    public function saveDiskVariables(DiskEnvironmentRequest $request)
    {
        $diskEnv = $this->getDiskConfiguration($request);

        try {

            $this->updateEnv($diskEnv);

        } catch (Exception $e) {
            return [
                'error' => 'disk_variables_save_error',
            ];
        }

        return [
            'success' => 'disk_variables_save_successfully',
        ];
    }

    /**
     * Returns the disk configuration
     *
     * @return array
     */
    private function getDiskConfiguration(DiskEnvironmentRequest $request)
    {
        $diskEnv = [];

        $driver = $request->get('app_domain');

        if ($driver) {
            $diskEnv['FILESYSTEM_DRIVER'] = $driver;
        }

        switch ($request->get('selected_driver')) {
            case 's3':

                $diskEnv = [
                    'AWS_KEY' => $request->get('aws_key'),
                    'AWS_SECRET' => $request->get('aws_secret'),
                    'AWS_REGION' => $request->get('aws_region'),
                    'AWS_BUCKET' => $request->get('aws_bucket'),
                    'AWS_ROOT' => $request->get('aws_root'),
                ];

                break;

            case 'doSpaces':

                $diskEnv = [
                    'DO_SPACES_KEY' => $request->get('do_spaces_key'),
                    'DO_SPACES_SECRET' => $request->get('do_spaces_secret'),
                    'DO_SPACES_REGION' => $request->get('do_spaces_region'),
                    'DO_SPACES_BUCKET' => $request->get('do_spaces_bucket'),
                    'DO_SPACES_ENDPOINT' => $request->get('do_spaces_endpoint'),
                    'DO_SPACES_ROOT' => $request->get('do_spaces_root'),
                ];

                break;

            case 'dropbox':

                $diskEnv = [
                    'DROPBOX_TOKEN' => $request->get('dropbox_token'),
                    'DROPBOX_KEY' => $request->get('dropbox_key'),
                    'DROPBOX_SECRET' => $request->get('dropbox_secret'),
                    'DROPBOX_APP' => $request->get('dropbox_app'),
                    'DROPBOX_ROOT' => $request->get('dropbox_root'),
                ];

                break;
        }

        return $diskEnv;
    }

    /**
     * Save sanctum stateful domain to the .env file.
     *
     * @return array
     */
    public function saveDomainVariables(DomainEnvironmentRequest $request)
    {
        try {
            $this->updateEnv([
                'SANCTUM_STATEFUL_DOMAINS' => $request->get('app_domain'),
                'SESSION_DOMAIN' => explode(':', $request->get('app_domain'))[0],
            ]);
        } catch (Exception $e) {
            return [
                'error' => 'domain_verification_failed',
            ];
        }

        return [
            'success' => 'domain_variable_save_successfully',
        ];
    }

    /**
     * Order the env contents
     * @return void
     */
    public function reoderEnv()
    {
        $contents = $this->getEnvContents();
        $contents = explode($this->delimiter, $contents);
        natsort($contents);

        $formatted = '';
        $previous = '';
        foreach($contents as $current) {
            $parts_line = explode('_', $current);
            $parts_last = explode('_', $previous);
            if($parts_line[0] != $parts_last[0]) {
                $formatted .= $this->delimiter;
            }
            $formatted .= $current.$this->delimiter;
            $previous = $current;

        }

        file_put_contents($this->envPath, trim($formatted));
    }
}
