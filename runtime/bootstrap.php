<?php

use Roukmoute\Polyfill\Calendar\Calendar;

if (!function_exists('easter_date')) {
    function easter_date($year = null)
    {
        return Calendar::easter_date($year);
    }
}
