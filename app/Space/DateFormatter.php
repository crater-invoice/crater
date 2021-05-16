<?php

namespace Crater\Space;

use Carbon\Carbon;

class DateFormatter
{
    protected static $formats = [
        [
            "carbon_format" => "Y M d",
            "moment_format" => "YYYY MMM DD",
        ],
        [
            "carbon_format" => "d M Y",
            "moment_format" => "DD MMM YYYY",
        ],
        [
            "carbon_format" => "d/m/Y",
            "moment_format" => "DD/MM/YYYY",
        ],
        [
            "carbon_format" => "d.m.Y",
            "moment_format" => "DD.MM.YYYY",
        ],
        [
            "carbon_format" => "d-m-Y",
            "moment_format" => "DD-MM-YYYY",
        ],
        [
            "carbon_format" => "m/d/Y",
            "moment_format" => "MM/DD/YYYY",
        ],
        [
            "carbon_format" => "Y/m/d",
            "moment_format" => " YYYY/MM/DD",
        ],
        [
            "carbon_format" => "Y-m-d",
            "moment_format" => "YYYY-MM-DD",
        ],
    ];

    public static function get_list()
    {
        $new = [];

        foreach (static::$formats as $format) {
            $new[] = [
                "display_date" => Carbon::now()->format($format['carbon_format']) ,
                "carbon_format_value" => $format['carbon_format'],
                "moment_format_value" => $format['moment_format'],
            ];
        }

        return $new;
    }
}
