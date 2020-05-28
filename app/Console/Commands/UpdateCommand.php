<?php

namespace Crater\Console\Commands;

use Illuminate\Console\Command;
use Crater\Space\Updater;
use Crater\Setting;

// Implementation taken from Akaunting - https://github.com/akaunting/akaunting
class UpdateCommand extends Command
{
    public $installed;

    public $version;

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
        $this->version = $this->getLatestVersion();

        if (!$this->version) {
            $this->info('No Update Available! You are already on the latest version.');
            return;
        }

        if (!$this->confirm("Do you wish to update to {$this->version}?")) {
            return;
        }

        if (!$path = $this->download()) {
            return;
        }

        if (!$path = $this->unzip($path)) {
            return;
        }

        if (!$this->copyFiles($path)) {
            return;
        }

        if (!$this->migrateUpdate()) {
            return;
        }

        if (!$this->finish()) {
            return;
        }

        $this->info('Successfully updated to ' . $this->version);
    }

    public function getInstalledVersion()
    {
        return Setting::getSetting('version');
    }

    public function getLatestVersion()
    {
        $this->info('Your currently installed version is ' . $this->installed);
        $this->line('');
        $this->info('Checking for update...');

        try {
            $response = Updater::checkForUpdate($this->installed);

            if ($response->success) {
                return $response->version->version;
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
            $path = Updater::download($this->version);
            if (!is_string($path)) {
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
            if (!is_string($path)) {
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
