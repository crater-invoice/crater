<?php

namespace Crater\Providers;

use Crater\Events\UpdateFinished;
use Crater\Listeners\Updates\v1\Version110;
use Crater\Listeners\Updates\v2\Version200;
use Crater\Listeners\Updates\v2\Version201;
use Crater\Listeners\Updates\v2\Version202;
use Crater\Listeners\Updates\v2\Version210;
use Crater\Listeners\Updates\v3\Version300;
use Crater\Listeners\Updates\v3\Version310;
use Crater\Listeners\Updates\v3\Version311;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UpdateFinished::class => [
            Version110::class,
            Version200::class,
            Version201::class,
            Version202::class,
            Version210::class,
            Version300::class,
            Version310::class,
            Version311::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
