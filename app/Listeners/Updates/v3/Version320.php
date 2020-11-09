<?php

namespace Crater\Listeners\Updates\v3;

use Crater\Listeners\Updates\Listener;
use Crater\Events\UpdateFinished;
use Crater\Setting;
use Crater\Currency;
use Artisan;

class Version320 extends Listener
{
    const VERSION = '3.2.0';

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

        Artisan::call('migrate', ['--force' => true]);

        // Update Crater app version
        Setting::setSetting('version', static::VERSION);
    }
}
