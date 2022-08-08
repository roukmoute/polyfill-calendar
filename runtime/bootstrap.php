<?php

declare(strict_types=1);

use Roukmoute\Polyfill\Calendar\Calendar;
use Roukmoute\Polyfill\Calendar\Easter;
use Roukmoute\Polyfill\Calendar\Jewish;
use Roukmoute\Polyfill\Calendar\Julian;

if (!function_exists('easter_date')) {
    function easter_date(int $year = null): int
    {
        return Easter::easter_date($year);
    }
}

if (!function_exists('easter_days')) {
    function easter_days(int $year = null): int
    {
        return Easter::easter_days($year);
    }
}

if (!function_exists('jdtogregorian')) {
    function jdtogregorian(int $julian_day): string
    {
        return Julian::jdtogregorian($julian_day);
    }
}

if (!function_exists('jewishtojd')) {
    function jewishtojd(int $month, int $day, int $year): int
    {
        return Jewish::jewishtojd($month, $day, $year);
    }
}

if (!function_exists('juliantojd')) {
    function juliantojd(int $month, int $day, int $year): int
    {
        return Julian::juliantojd($month, $day, $year);
    }
}

if (!function_exists('cal_to_jd')) {
    function cal_to_jd(int $calendar, int $month, int $day, int $year)
    {
        return Calendar::cal_to_jd($calendar, $month, $day, $year);
    }
}
