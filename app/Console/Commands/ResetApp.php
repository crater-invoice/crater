<?php

namespace Crater\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Artisan;

class ResetApp extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:app {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean database, database_created and public/storage folder';

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
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $this->info('Running migrate:fresh');

        Artisan::call('migrate:fresh --seed --force');

        $this->info('Seeding database');

        Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

        $path = base_path('.env');

        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                'APP_DEBUG=true',
                'APP_DEBUG=false',
                file_get_contents($path)
            ));
        }

        $this->info('App has been reset successfully');
    }
}
