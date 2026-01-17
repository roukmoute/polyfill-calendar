<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

/**
 * French Serial Day Number (SDN) conversions
 *
 * @see https://github.com/php/php-src/blob/PHP-8.1.9/ext/calendar/french.c
 */
class French implements SDNConversions
{
    private const FRENCH_SDN_OFFSET = 2375474;
    private const DAYS_PER_4_YEARS = 1461;
    private const DAYS_PER_MONTH = 30;

    /**
     * Converts a date from the French Republican Calendar to a Julian Day Count.
     *
     * @see https://www.php.net/manual/en/function.frenchtojd.php
     */
    public static function frenchtojd(int $month, int $day, int $year): int
    {
        return self::toSDN($year, $month, $day);
    }

    /**
     * Converts a Julian Day Count to French Republican Calendar Date.
     *
     * @see https://www.php.net/manual/en/function.jdtofrench.php
     */
    public static function jdtofrench(int $julian_day): string
    {
        /* French Republican calendar valid range: year 1-14 */
        /* First valid: JD 2375840 (1/1/1), Last valid: JD 2380952 (13/5/14) */
        if ($julian_day < 2375840 || $julian_day > 2380952) {
            return '0/0/0';
        }

        $temp = ($julian_day - self::FRENCH_SDN_OFFSET) * 4 - 1;
        $year = (int) ($temp / self::DAYS_PER_4_YEARS);
        $dayOfYear = (int) (($temp % self::DAYS_PER_4_YEARS) / 4);

        $month = (int) ($dayOfYear / self::DAYS_PER_MONTH) + 1;
        $day = ($dayOfYear % self::DAYS_PER_MONTH) + 1;

        return "{$month}/{$day}/{$year}";
    }

    /**
     * Convert a French republican calendar date to a SDN.
     * {@inheritDoc}
     */
    public static function toSDN(int $year, int $month, int $day): int
    {
        /* check for invalid dates */
        if ($year < 1 || $year > 14
            || $month < 1
            || $month > 13
            || $day < 1
            || $day > 30) {
            return 0;
        }

        /* Month 13 (Sansculottides) has only 5 or 6 days */
        if ($month === 13) {
            /* Leap years: 3, 7, 11 have 6 days, others have 5 */
            $maxDay = ($year === 3 || $year === 7 || $year === 11) ? 6 : 5;
            if ($day > $maxDay) {
                return 0;
            }
        }

        return (int) (($year * self::DAYS_PER_4_YEARS) / 4)
            + ($month - 1) * self::DAYS_PER_MONTH
            + $day
            + self::FRENCH_SDN_OFFSET;
    }
}
