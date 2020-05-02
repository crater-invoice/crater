<?php

namespace Crater\Listeners\Updates\v3;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Crater\Listeners\Updates\Listener;
use Illuminate\Database\Schema\Blueprint;
use Crater\Events\UpdateFinished;
use Crater\Setting;
use Crater\Currency;
use Schema;

class Version310 extends Listener
{
    const VERSION = '3.1.0';

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

        if (!Schema::hasColumn('expenses', 'user_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Update Crater app version
        Setting::setSetting('version', static::VERSION);
    }
}
