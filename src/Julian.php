<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

/**
 * Julian Serial Day Number (SDN) conversions
 *
 * @see https://github.com/php/php-src/blob/PHP-8.1.9/ext/calendar/julian.c
 */
final class Julian implements SDNConversions
{
    public const JULIAN_SDN_OFFSET = 32083;
    public const DAYS_PER_5_MONTHS = 153;
    public const DAYS_PER_4_YEARS = 1461;

    /**
     * @author treebe
     * @see https://www.php.net/manual/en/function.jdtogregorian.php#32561
     */
    public static function jdtogregorian(int $julian_day): string
    {
        if ($julian_day <= 0 || $julian_day >= 536838867) {
            return '0/0/0';
        }

        $julian = $julian_day - 1721119;
        $julian %= 535117748;
        $calc1 = 4 * $julian - 1;
        $year = floor($calc1 / 146097);
        $julian = floor($calc1 - 146097 * $year);
        $day = floor($julian / 4);
        $calc2 = 4 * $day + 3;
        $julian = floor($calc2 / 1461);
        $day = $calc2 - 1461 * $julian;
        $day = floor(($day + 4) / 4);
        $calc3 = 5 * $day - 3;
        $month = floor($calc3 / 153);
        $day = $calc3 - 153 * $month;
        $day = floor(($day + 5) / 5);
        $year = 100 * $year + $julian;

        if ($month < 10) {
            $month += 3;
        } else {
            $month -= 9;
            ++$year;
        }

        if ($year <= 0) {
            --$year;
        }

        return "{$month}/{$day}/{$year}";
    }

    /**
     * Converts a Julian Day Count to a Julian Calendar Date.
     *
     * @see https://www.php.net/manual/en/function.jdtojulian.php
     */
    public static function jdtojulian(int $julian_day): string
    {
        if ($julian_day <= 0) {
            return '0/0/0';
        }

        $temp = ($julian_day + self::JULIAN_SDN_OFFSET) * 4 - 1;
        $year = (int) ($temp / self::DAYS_PER_4_YEARS);
        $dayOfYear = (int) (($temp % self::DAYS_PER_4_YEARS) / 4) + 1;

        $temp = $dayOfYear * 5 - 3;
        $month = (int) ($temp / self::DAYS_PER_5_MONTHS);
        $day = (int) (($temp % self::DAYS_PER_5_MONTHS) / 5) + 1;

        if ($month < 10) {
            $month += 3;
        } else {
            $month -= 9;
            ++$year;
        }

        $year -= 4800;
        if ($year <= 0) {
            --$year;
        }

        return "{$month}/{$day}/{$year}";
    }

    /**
     * @author Scott E. Lee
     * @see https://github.com/php/php-src/blob/5b01c4863fe9e4bc2702b2bbf66d292d23001a18/ext/calendar/julian.c
     */
    public static function juliantojd(int $month, int $day, int $year): int
    {
        /* check for invalid dates */
        if ($year === 0 || $year < -4713 || $month <= 0 || $month > 12 || $day <= 0 || $day > 31) {
            return 0;
        }

        /* check for dates before SDN 1 (Jan 2, 4713 B.C.) */
        if ($year === -4713 && $month === 1 && $day == 1) {
            return 0;
        }

        /* Make year always a positive number. */
        $rYear = $year + ($year < 0 ? 4801 : 4800);

        /* Adjust the start of the year. */
        if ($month > 2) {
            $rMonth = $month - 3;
        } else {
            $rMonth = $month + 9;
            --$rYear;
        }

        return (int) (($rYear * self::DAYS_PER_4_YEARS) / 4)
            + (int) (($rMonth * self::DAYS_PER_5_MONTHS + 2) / 5)
            + $day
            - self::JULIAN_SDN_OFFSET;
    }

    /**
     * Convert a Julian calendar date to a SDN.
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

        return (int) (($year * self::DAYS_PER_4_YEARS) / 4)
            + (int) (($month * self::DAYS_PER_5_MONTHS + 2) / 5)
            + $day
            - self::JULIAN_SDN_OFFSET;
    }
}
