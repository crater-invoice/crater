<?php

namespace Crater\Space;

use Carbon\Carbon;

class TimeFormatter
{
    protected static $formats = [
        [
            "carbon_format" => "H:i",
            "moment_format" => "HH:mm",
        ],
        [
            "carbon_format" => "g:i a",
            "moment_format" => "h:mm a",
        ],
    ];

    public static function get_list()
    {
        $new = [];

        foreach (static::$formats as $format) {
            $new[] = [
                "display_time" => Carbon::now()->format($format['carbon_format']) ,
                "carbon_format_value" => $format['carbon_format'],
                "moment_format_value" => $format['moment_format'],
            ];
        }

        return $new;
    }
}
