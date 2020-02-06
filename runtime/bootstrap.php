<?php

use Roukmoute\Polyfill\Calendar\Calendar;

if (!function_exists('easter_date')) {
    function easter_date($year = null)
    {
        return Calendar::easter_date($year);
    }
}

if (!function_exists('easter_days')) {
    function easter_days($year = null)
    {
        return Calendar::easter_days($year);
    }
}
