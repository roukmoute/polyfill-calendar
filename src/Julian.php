<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

final class Julian
{
    const JULIAN_SDN_OFFSET = 32083;
    const DAYS_PER_5_MONTHS = 153;
    const DAYS_PER_4_YEARS = 1461;

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
            $month = $month + 3;
        } else {
            $month = $month - 9;
            $year++;
        }

        if ($year <= 0) {
            $year--;
        }

        return "$month/$day/$year";
    }

    /**
     * @author Scott E. Lee
     * @see https://github.com/php/php-src/blob/5b01c4863fe9e4bc2702b2bbf66d292d23001a18/ext/calendar/julian.c
     */
    public static function juliantojd(int $month, int $day, int $year): int
    {
        /* check for invalid dates */
        if ($year === 0 || $year < -4713 ||
            $month <= 0 || $month > 12 ||
            $day <= 0 || $day > 31
        ) {
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
            $rYear--;
        }

        return ((int) (($rYear * self::DAYS_PER_4_YEARS) / 4)
            + (int) (($rMonth * self::DAYS_PER_5_MONTHS + 2) / 5)
            + $day
            - self::JULIAN_SDN_OFFSET);
    }
}
