<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

/**
 * French Serial Day Number (SDN) conversions
 *
 * @see https://github.com/php/php-src/blob/PHP-8.1.9/ext/calendar/french.c
 */
interface SDNConversions
{
    /**
     * Convert a specific calendar date to a SDN. Zero is returned
     * when the input date is detected as invalid or out of the supported
     * range. The return value will be > 0 for all valid, supported dates, but
     * there are some invalid dates that will return a positive value. To
     * verify that a date is valid, convert it to SDN and then back and compare
     * with the original.
     */
    public static function toSDN(int $year, int $month, int $day): int;
}
