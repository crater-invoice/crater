<?php

namespace Crater\Listeners\Updates\v3;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Database\Schema\Blueprint;
use Crater\Events\UpdateFinished;
use Crater\Listeners\Updates\Listener;
use Crater\Setting;
use Crater\Currency;

class Version301 extends Listener
{
    const VERSION = '3.0.1';

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

        Currency::create([
            'name' => 'Kyrgyzstani som',
            'code' => 'KGS',
            'symbol' => 'С̲ ',
            'precision' => '2',
            'thousand_separator' => '.',
            'decimal_separator' => ','
        ]);

        // Update Crater app version
        Setting::setSetting('version', static::VERSION);
    }
}
