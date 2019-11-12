<?php

namespace Laraspace\Listeners\Updates\V10;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laraspace\Listeners\Updates\Listener;
use Laraspace\Events\UpdateFinished;

class Version101 extends Listener
{
    const ALIAS = 'core';

    const VERSION = '1.0.1';

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
    }
}
