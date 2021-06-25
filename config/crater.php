<?php

return [

    /*
    * Minimum php version.
    */
    'min_php_version' => '7.4.0',

    /*
    * Minimum mysql version.
    */

    'min_mysql_version' => '5.7.7',

    /*
    * Minimum mariadb version.
    */

    'min_mariadb_version' => '10.2.7',

    /*
    * Minimum pgsql version.
    */

    'min_pgsql_version' => '9.2.0',

    /*
    * Minimum sqlite version.
    */

    'min_sqlite_version' => '3.24.0',

    /*
    * List of languages supported by Crater.
    */
    'languages' => [
        ["code" => "ar", "name" => "Arabic"],
        ["code" => "nl", "name" => "Dutch"],
        ["code" => "en", "name" => "English"],
        ["code" => "fr", "name" => "French"],
        ["code" => "de", "name" => "German"],
        ["code" => "ja", "name" => "Japanese"],
        ["code" => "it", "name" => "Italian"],
        ["code" => "lv", "name" => "Latvian"],
        ["code" => "pt_BR", "name" => "Portuguese (Brazilian)"],
        ["code" => "sr", "name" => "Serbian Latin"],
        ["code" => "ko", "name" => "Korean"],
        ["code" => "es", "name" => "Spanish"],
        ["code" => "sv", "name" => "Svenska"],
        ["code" => "sk", "name" => "Slovak"],
        ["code" => "vi", "name" => "Tiếng Việt"],
    ],

    /*
    * List of Fiscal Years
    */
    'fiscal_years' => [
        ['key' => 'january-december' , 'value' => '1-12'],
        ['key' => 'february-january' , 'value' => '2-1'],
        ['key' => 'march-february'   , 'value' => '3-2'],
        ['key' => 'april-march'      , 'value' => '4-3'],
        ['key' => 'may-april'        , 'value' => '5-4'],
        ['key' => 'june-may'         , 'value' => '6-5'],
        ['key' => 'july-june'        , 'value' => '7-6'],
        ['key' => 'august-july'      , 'value' => '8-7'],
        ['key' => 'september-august' , 'value' => '9-8'],
        ['key' => 'october-september', 'value' => '10-9'],
        ['key' => 'november-october' , 'value' => '11-10'],
        ['key' => 'december-november', 'value' => '12-11'],
    ],
];
