<?php

namespace Crater\Listeners\Updates\v3;

use Artisan;
use Crater\Events\UpdateFinished;
use Crater\Listeners\Updates\Listener;
use Crater\Models\Currency;
use Crater\Models\Setting;

class Version310 extends Listener
{
    public const VERSION = '3.1.0';

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
                'code' => 'KGS',
            ],
            [
                'name' => 'Kyrgyzstani som',
                'code' => 'KGS',
                'symbol' => 'С̲ ',
                'precision' => '2',
                'thousand_separator' => '.',
                'decimal_separator' => ',',
            ]
        );

        Artisan::call('migrate', ['--force' => true]);

        // Update Crater app version
        Setting::setSetting('version', static::VERSION);
    }
}
