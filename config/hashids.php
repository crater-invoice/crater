<?php

/**
 * Copyright (c) Vincent Klaiber.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/vinkla/laravel-hashids
 */

use Crater\Models\Estimate;
use Crater\Models\Invoice;
use Crater\Models\Payment;

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
            'length' => '20',
            'alphabet' => 'XKyIAR7mgt8jD2vbqPrOSVenNGpiYLx4M61T',
        ],
        Estimate::class => [
            'salt' => Estimate::class.config('app.key'),
            'length' => '20',
            'alphabet' => 'yLJWP79M8rYVqbn1NXjulO6IUDdvekRQGo40',
        ],
        Payment::class => [
            'salt' => Payment::class.config('app.key'),
            'length' => '20',
            'alphabet' => 'asqtW3eDRIxB65GYl7UVLS1dybn9XrKTZ4zO',
        ],
    ],

];
