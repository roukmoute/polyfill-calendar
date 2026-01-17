<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

/**
 * Gregorian Serial Day Number (SDN) conversions
 *
 * @see https://github.com/php/php-src/blob/PHP-8.1.9/ext/calendar/gregor.c
 */
class Gregor implements SDNConversions
{
    private const GREGOR_SDN_OFFSET = 32045;
    private const DAYS_PER_5_MONTHS = 153;
    private const DAYS_PER_4_YEARS = 1461;
    private const DAYS_PER_400_YEARS = 146097;

    /**
     * Converts a Gregorian date to Julian Day Count.
     *
     * @see https://www.php.net/manual/en/function.gregoriantojd.php
     */
    public static function gregoriantojd(int $month, int $day, int $year): int
    {
        return self::toSDN($year, $month, $day);
    }

    /**
     * Converts a Julian Day Count to a Gregorian date.
     *
     * @return array{int, int, int} [year, month, day]
     */
    public static function sdnToGregorian(int $sdn): array
    {
        if ($sdn <= 0) {
            return [0, 0, 0];
        }

        $temp = ($sdn + self::GREGOR_SDN_OFFSET) * 4 - 1;

        /* Calculate the century (year/100). */
        $century = (int) ($temp / self::DAYS_PER_400_YEARS);

        /* Calculate the year and day of year (1 <= dayOfYear <= 366). */
        $temp = ((int) (($temp % self::DAYS_PER_400_YEARS) / 4)) * 4 + 3;
        $year = ($century * 100) + (int) ($temp / self::DAYS_PER_4_YEARS);
        $dayOfYear = (int) (($temp % self::DAYS_PER_4_YEARS) / 4) + 1;

        /* Calculate the month and day of month. */
        $temp = $dayOfYear * 5 - 3;
        $month = (int) ($temp / self::DAYS_PER_5_MONTHS);
        $day = (int) (($temp % self::DAYS_PER_5_MONTHS) / 5) + 1;

        /* Convert to the normal beginning of the year. */
        if ($month < 10) {
            $month += 3;
        } else {
            $month -= 9;
            ++$year;
        }

        /* Adjust to the B.C./A.D. type numbering. */
        $year -= 4800;
        if ($year <= 0) {
            --$year;
        }

        return [$year, $month, $day];
    }

    /**
     * Convert a Gregorian calendar date to a SDN.
     * {@inheritDoc}
     */
    public static function toSDN(int $year, int $month, int $day): int
    {
        /* check for invalid dates */
        if ($year === 0 || $year < -4714
            || $month <= 0
            || $month > 12
            || $day <= 0
            || $day > 31) {
            return 0;
        }

        /* check for dates before SDN 1 (Nov 25, 4714 B.C.) */
        if ($year === -4714) {
            if ($month < 11) {
                return 0;
            }
            if ($month === 11 && $day < 25) {
                return 0;
            }
        }
        /* Make $year always a positive number. */
        if ($year < 0) {
            $year += 4801;
        } else {
            $year += 4800;
        }

        /* Adjust the start of the $year. */
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            --$year;
        }

        return (int) (((int) ($year / 100) * self::DAYS_PER_400_YEARS) / 4)
            + (int) ((($year % 100) * self::DAYS_PER_4_YEARS) / 4)
            + (int) (($month * self::DAYS_PER_5_MONTHS + 2) / 5)
            + $day
            - self::GREGOR_SDN_OFFSET;
    }
}
