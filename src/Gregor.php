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
     * Convert a Gregorian republican calendar date to a SDN.
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
            $year = $year + 4801;
        } else {
            $year = $year + 4800;
        }

        /* Adjust the start of the $year. */
        if ($month > 2) {
            $month = $month - 3;
        } else {
            $month = $month + 9;
            --$year;
        }

        return (int) (((int) ($year / 100) * self::DAYS_PER_400_YEARS) / 4)
            + (int) ((($year % 100) * self::DAYS_PER_4_YEARS) / 4)
            + (int) (($month * self::DAYS_PER_5_MONTHS + 2) / 5)
            + $day
            - self::GREGOR_SDN_OFFSET;
    }
}
