<?php

namespace Crater\Listeners\Updates\v1;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Crater\Events\UpdateFinished;
use Crater\Setting;

class Version110
{
    const VERSION = '1.1.0';

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
        if (!$this->check($event)) {
            return;
        }

        // Update Crater app version
        Setting::setSetting('version', static::VERSION);
    }
}
