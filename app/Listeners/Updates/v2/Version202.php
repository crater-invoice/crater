<?php

namespace Crater\Listeners\Updates\v2;

use Crater\Events\UpdateFinished;
use Crater\Listeners\Updates\Listener;
use Crater\Models\Setting;

class Version202 extends Listener
{
    public const VERSION = '2.0.2';

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

        // Update Crater app version
        Setting::setSetting('version', static::VERSION);
    }
}
