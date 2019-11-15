<?php

namespace Crater\Listeners\Updates\V10;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Crater\Listeners\Updates\Listener;
use Crater\Events\UpdateFinished;
use Illuminate\Support\Facades\Artisan;
use Crater\Setting;

class Version101 extends Listener
{
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

        Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

        Setting::setSetting('version', self::VERSION);
    }
}
