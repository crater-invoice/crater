<?php

namespace Crater\Listeners\Updates\v3;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Crater\Listeners\Updates\Listener;
use Illuminate\Database\Schema\Blueprint;
use Crater\Events\UpdateFinished;
use Crater\Models\Setting;
use Crater\Models\Currency;
use Schema;
use Artisan;

class Version310 extends Listener
{
    const VERSION = '3.1.0';


    /**
     * Handle the event.
     *
     * @param UpdateFinished $event
     * @return void
     */
    public function handle(UpdateFinished $event)
    {
        if ($this->isListenerFired($event)) {
            return;
        }

        Currency::firstOrCreate(
            [
                'name' => 'Kyrgyzstani som',
                'code' => 'KGS'
            ],
            [
                'name' => 'Kyrgyzstani som',
                'code' => 'KGS',
                'symbol' => 'С̲ ',
                'precision' => '2',
                'thousand_separator' => '.',
                'decimal_separator' => ','
            ]
        );

        Artisan::call('migrate', ['--force' => true]);

        // Update Crater app version
        Setting::setSetting('version', static::VERSION);
    }
}
