<?php

namespace Crater\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CreateTemplateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:template {name} {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create estimate or invoice pdf template.                               ';

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
     * @return int
     */
    public function handle()
    {
        $templateName = $this->argument('name');
        $type = $this->option('type');

        if (! $type) {
            $type = $this->choice('Create a template for?', ['invoice', 'estimate']);
        }

        if (Storage::disk('views')->exists("/app/pdf/{$type}/{$templateName}.blade.php")) {
            $this->info("Template with given name already exists.");

            return 0;
        }

        Storage::disk('views')->copy("/app/pdf/{$type}/{$type}1.blade.php", "/app/pdf/{$type}/{$templateName}.blade.php");
        copy(public_path("/build/img/PDF/{$type}1.png"), public_path("/build/img/PDF/{$templateName}.png"));
        copy(resource_path("/static/img/PDF/{$type}1.png"), resource_path("/static/img/PDF/{$templateName}.png"));

        $path = resource_path("views/app/pdf/{$type}/{$templateName}.blade.php");
        $type = ucfirst($type);
        $this->info("{$type} Template created successfully at ".$path);

        return 0;
    }
}
