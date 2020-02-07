<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

/**
 * Based on code by Simon Kershaw <simon@oremus.org>
 * @see: http://easter.oremus.org/when/bradley.html
 */
class Calendar
{
    private const MARCH = 3;
    private const APRIL = 4;

    /**
     * Return the timestamp of midnight on Easter of a given year (defaults to current year)
     */
    public static function easter_date(int $year = null): int
    {
        if (!$year) {
            $year = (int) date('Y');
        }

        $easter = self::calEaster($year, true);

        if ($easter < 11) {
            $month = self::MARCH;
            $day = $easter + 21;
        } else {
            $month = self::APRIL;
            $day = $easter - 10;
        }

        return mktime(0, 0, 0, $month, $day, $year);
    }

    /**
     * Return the number of days after March 21 that Easter falls on for a given year (defaults to current year)
     */
    public static function easter_days(int $year = null): int
    {
        return self::calEaster($year, false);
    }

    private static function calEaster(int $year = null, bool $isEasterDate): int
    {
        if (!$year) {
            $year = (int) date('Y');
        }

        if (self::isOutOfRange($year, $isEasterDate)) {
            throw new ValueError('This function is only valid for years between 1970 and 2037 inclusive');
        }

        /* the Golden number */
        $golden = ($year % 19) + 1;

        if (self::isJulianCalendar($year)) {
            /* the "Dominical number" - finding a Sunday */
            $dominicalNumber = ($year + ($year / 4) + 5) % 7;
            if ($dominicalNumber < 0) {
                $dominicalNumber += 7;
            }

            $paschalFullMoon = (3 - (11 * $golden) - 7) % 30;  /* uncorrected date of the Paschal full moon */
            if ($paschalFullMoon < 0) {
                $paschalFullMoon += 30;
            }
        } else { /* Gregorian Calendar */
            $dominicalNumber = ($year + ($year / 4) - ($year / 100) + ($year / 400)) % 7;
            if ($dominicalNumber < 0) {
                $dominicalNumber += 7;
            }

            /* the solar and lunar corrections */
            $solar = ($year - 1600) / 100 - ($year - 1600) / 400;
            $lunar = ((($year - 1400) / 100) * 8) / 25;

            /* uncorrected date of the Paschal full moon */
            $paschalFullMoon = (3 - (11 * $golden) + $solar - $lunar) % 30;
            if ($paschalFullMoon < 0) {
                $paschalFullMoon += 30;
            }
        }

        /* corrected date of the Paschal full moon */
        if (($paschalFullMoon == 29) || ($paschalFullMoon == 28 && $golden > 11)) {
            /* days after 21st March */
            --$paschalFullMoon;
        }

        $paschalFullMoonPrime = (4 - $paschalFullMoon - $dominicalNumber) % 7;
        if ($paschalFullMoonPrime < 0) {
            $paschalFullMoonPrime += 7;
        }

        /* Easter as the number of days after 21st March */
        return $paschalFullMoon + $paschalFullMoonPrime + 1;
    }

    /**
     * Out of range for timestamps
     */
    private static function isOutOfRange(int $year, bool $isEasterDate): bool
    {
        return $isEasterDate && ($year < 1970 || $year > 2037);
    }

    /**
     * Julian Calendar (for years before 1753)
     */
    private static function isJulianCalendar(int $year): bool
    {
        return $year <= 1752;
    }
}
