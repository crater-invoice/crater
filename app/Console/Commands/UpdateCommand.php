<?php

namespace Crater\Console\Commands;

use Crater\Models\Setting;
use Crater\Space\Updater;
use Illuminate\Console\Command;

// Implementation taken from Akaunting - https://github.com/akaunting/akaunting
class UpdateCommand extends Command
{
    public $installed;

    public $version;

    public $response;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crater:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update your crater app';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(3600); // 1 hour

        $this->installed = $this->getInstalledVersion();
        $this->response = $this->getLatestVersionResponse();
        $this->version = ($this->response) ? $this->response->version : false;

        if ($this->response == 'extension_required') {
            $this->info('Sorry! Your system does not meet the minimum requirements for this update.');
            $this->info('Please retry after installing the required version/extensions.');

            return;
        }

        if (! $this->version) {
            $this->info('No Update Available! You are already on the latest version.');

            return;
        }

        if (! $this->confirm("Do you wish to update to {$this->version}?")) {
            return;
        }

        if (! $path = $this->download()) {
            return;
        }

        if (! $path = $this->unzip($path)) {
            return;
        }

        if (! $this->copyFiles($path)) {
            return;
        }

        if (isset($this->response->deleted_files) && ! empty($this->response->deleted_files)) {
            if (! $this->deleteFiles($this->response->deleted_files)) {
                return;
            }
        }

        if (! $this->migrateUpdate()) {
            return;
        }

        if (! $this->finish()) {
            return;
        }

        $this->info('Successfully updated to '.$this->version);
    }

    public function getInstalledVersion()
    {
        return Setting::getSetting('version');
    }

    public function getLatestVersionResponse()
    {
        $this->info('Your currently installed version is '.$this->installed);
        $this->line('');
        $this->info('Checking for update...');

        try {
            $response = Updater::checkForUpdate($this->installed);

            if ($response->success) {
                $extensions = $response->version->extensions;

                $is_required = false;

                foreach ($extensions as $key => $extension) {
                    if (! $extension) {
                        $is_required = true;
                        $this->info('❌ '.$key);
                    }

                    $this->info('✅ '.$key);
                }

                if ($is_required) {
                    return 'extension_required';
                }

                return $response->version;
            }

            return false;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return false;
        }
    }

    public function download()
    {
        $this->info('Downloading update...');

        try {
            $path = Updater::download($this->version, 1);
            if (! is_string($path)) {
                $this->error('Download exception');

                return false;
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return false;
        }

        return $path;
    }

    public function unzip($path)
    {
        $this->info('Unzipping update package...');

        try {
            $path = Updater::unzip($path);
            if (! is_string($path)) {
                $this->error('Unzipping exception');

                return false;
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return false;
        }

        return $path;
    }

    public function copyFiles($path)
    {
        $this->info('Copying update files...');

        try {
            Updater::copyFiles($path);
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return false;
        }

        return true;
    }

    public function deleteFiles($files)
    {
        $this->info('Deleting unused old files...');

        try {
            Updater::deleteFiles($files);
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return false;
        }

        return true;
    }

    public function migrateUpdate()
    {
        $this->info('Running Migrations...');

        try {
            Updater::migrateUpdate();
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return false;
        }

        return true;
    }

    public function finish()
    {
        $this->info('Finishing update...');

        try {
            Updater::finishUpdate($this->installed, $this->version);
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return false;
        }

        return true;
    }
}
