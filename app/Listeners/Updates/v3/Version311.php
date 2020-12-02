<?php

namespace Crater\Listeners\Updates\v3;

use Crater\Listeners\Updates\Listener;
use Crater\Events\UpdateFinished;
use Crater\Models\Setting;
use Crater\Models\Currency;
use Artisan;

class Version311 extends Listener
{
    const VERSION = '3.1.1';

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
