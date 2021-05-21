<?php

namespace Crater\Listeners\Updates\v1;

use Crater\Events\UpdateFinished;
use Crater\Listeners\Updates\Listener;
use Crater\Models\Currency;
use Crater\Models\Setting;

class Version110 extends Listener
{
    public const VERSION = '1.1.0';

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

        // Add currencies
        $this->addCurrencies();

        // Update Crater app version
        Setting::setSetting('version', static::VERSION);
    }

    private function addCurrencies()
    {
        $currencies = [
            '13' => [
                'symbol' => 'S$',
            ],
            '16' => [
                'symbol' => '₫',
            ],
            '17' => [
                'symbol' => 'Fr.',
            ],
            '21' => [
                'symbol' => '฿',
            ],
            '22' => [
                'symbol' => '₦',
            ],
            '26' => [
                'symbol' => 'HK$',
            ],
            '35' => [
                'symbol' => 'NAƒ',
            ],
            '38' => [
                'symbol' => 'GH₵',
            ],
            '39' => [
                'symbol' => 'Лв.',
            ],
            '42' => [
                'symbol' => 'RON',
            ],
            '44' => [
                'symbol' => 'SِAR',
            ],
            '46' => [
                'symbol' => 'Rf',
            ],
            '47' => [
                'symbol' => '₡',
            ],
            '54' => [
                'symbol' => '‎د.ت',
            ],
            '55' => [
                'symbol' => '₽',
            ],
            '57' => [
                'symbol' => 'ر.ع.',
            ],
            '58' => [
                'symbol' => '₴',
            ],

        ];

        foreach ($currencies as $key => $currency) {
            Currency::updateOrCreate(['id' => $key], $currency);
        }

        Currency::create([
            'name' => 'Kuwaiti Dinar',
            'code' => 'KWD',
            'symbol' => 'KWD ',
            'precision' => '3',
            'thousand_separator' => ',',
            'decimal_separator' => '.',
        ]);
    }
}
