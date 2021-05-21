<?php

namespace Crater\Listeners\Updates\v2;

use Crater\Events\UpdateFinished;
use Crater\Listeners\Updates\Listener;
use Crater\Models\CompanySetting;
use Crater\Models\Setting;

class Version210 extends Listener
{
    public const VERSION = '2.1.0';

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

        // Add initial auto generate value
        $this->addAutoGenerateSettings();

        // Update Crater app version
        Setting::setSetting('version', static::VERSION);
    }

    private function addAutoGenerateSettings()
    {
        $settings = [
            'invoice_auto_generate' => 'YES',
            'invoice_prefix' => 'INV',
            'estimate_prefix' => 'EST',
            'estimate_auto_generate' => 'YES',
            'payment_prefix' => 'PAY',
            'payment_auto_generate' => 'YES',
        ];

        foreach ($settings as $key => $value) {
            CompanySetting::setSetting(
                $key,
                $value,
                auth()->user()->company->id
            );
        }
    }
}
