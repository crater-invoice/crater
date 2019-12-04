<?php

namespace Crater\Listeners\Updates\v2;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Database\Schema\Blueprint;
use Crater\Events\UpdateFinished;
use Crater\Listeners\Updates\Listener;
use Crater\Setting;

class Version201 extends Listener
{
    const VERSION = '2.0.1';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UpdateFinished $event)
    {
        if ($this->isListenerFired($event)) {
            return;
        }

        // Remove the language files
        $this->removeLanguageFiles();

        // Change estimate & invoice migrations
        $this->changeMigrations();

        // Update Crater app version
        Setting::setSetting('version', static::VERSION);
    }

    private function removeLanguageFiles() {
        $en = resource_path('assets/js/plugins/en.js');
        $es = resource_path('assets/js/plugins/es.js');
        $fr = resource_path('assets/js/plugins/fr.js');

        if(file_exists($en)) {
            unlink($en);
        }

        if(file_exists($es)) {
            unlink($es);
        }

        if(file_exists($fr)) {
            unlink($fr);
        }
    }

    private function changeMigrations()
    {
        \Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->nullable()->change();
        });

        \Schema::table('estimates', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->nullable()->change();
        });

        \Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->change();
            $table->decimal('discount', 15, 2)->nullable()->change();
        });

        \Schema::table('estimate_items', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->change();
            $table->decimal('discount', 15, 2)->nullable()->change();
            $table->unsignedBigInteger('discount_val')->nullable()->change();
        });
    }
}
