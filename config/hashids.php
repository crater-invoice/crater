<?php

/**
 * Copyright (c) Vincent Klaiber.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/vinkla/laravel-hashids
 */

use InvoiceShelf\Models\Company;
use InvoiceShelf\Models\EmailLog;
use InvoiceShelf\Models\Estimate;
use InvoiceShelf\Models\Invoice;
use InvoiceShelf\Models\Payment;
use InvoiceShelf\Models\Transaction;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [
        Invoice::class => [
            'salt' => Invoice::class.config('app.key'),
            'length' => 20,
            'alphabet' => 'XKAR7m8jD2bqP9OSVeNGiYL465T10zhfWuc3',
        ],
        Estimate::class => [
            'salt' => Estimate::class.config('app.key'),
            'length' => 20,
            'alphabet' => 'yJW2P79M8rCHsVq5zbn1fXl6IUt3dAekGo40',
        ],
        Payment::class => [
            'salt' => Payment::class.config('app.key'),
            'length' => 20,
            'alphabet' => 'aqW3eR2Icf0jp65Gl7UVS1dhyb8Mn9XKTZ4O',
        ],
        Company::class => [
            'salt' => Company::class.config('app.key'),
            'length' => 20,
            'alphabet' => 's0D7xOFYEqn2uKJm3Pr9g8Cz46A1iHLBTVW5',
        ],
        EmailLog::class => [
            'salt' => EmailLog::class.config('app.key'),
            'length' => 20,
            'alphabet' => 'BA5tJUVNPe93fCq6DHlY2x4ZO1Kg7i8wSm0R',
        ],
        Transaction::class => [
            'salt' => Transaction::class.config('app.key'),
            'length' => 20,
            'alphabet' => 'ADyWE86Cg7jF23vS0bonXrZ5KLH9puIQ4M1T',
        ],
    ],
];
