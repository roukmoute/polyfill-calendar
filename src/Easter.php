<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

/**
 * Based on code by Simon Kershaw <simon@oremus.org>
 *
 * @see http://easter.oremus.org/when/bradley.html
 * @see https://github.com/php/php-src/blob/master/ext/calendar/easter.c
 */
final class Easter
{
    public const CAL_EASTER_DEFAULT = 0;
    public const CAL_EASTER_ROMAN = 1;
    public const CAL_EASTER_ALWAYS_GREGORIAN = 2;
    public const CAL_EASTER_ALWAYS_JULIAN = 3;

    private const MARCH = 3;
    private const APRIL = 4;

    /**
     * Return the timestamp of midnight on Easter of a given year (defaults to current year)
     *
     * @see https://www.php.net/manual/en/function.easter-date.php
     */
    public static function easter_date(?int $year = null, int $mode = self::CAL_EASTER_DEFAULT): int
    {
        if ($year === null) {
            $year = (int) date('Y');
        }

        $easter = self::calEaster($year, true, $mode);

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
     *
     * @see https://www.php.net/manual/en/function.easter-days.php
     */
    public static function easter_days(?int $year = null, int $mode = self::CAL_EASTER_DEFAULT): int
    {
        return self::calEaster($year, false, $mode);
    }

    private static function calEaster(?int $year, bool $isEasterDate, int $mode = self::CAL_EASTER_DEFAULT): int
    {
        if ($year === null) {
            $year = (int) date('Y');
        }

        if (self::isOutOfRange($year, $isEasterDate)) {
            throw new ValueError('This function is only valid for years between 1970 and 2037 inclusive');
        }

        /* the Golden number */
        $golden = ($year % 19) + 1;

        if (self::useJulianCalendar($year, $mode)) {
            /* the "Dominical number" - finding a Sunday */
            $dominicalNumber = ($year + ((int) ($year / 4)) + 5) % 7;
            if ($dominicalNumber < 0) {
                $dominicalNumber += 7;
            }

            $paschalFullMoon = (3 - (11 * $golden) - 7) % 30;  /* uncorrected date of the Paschal full moon */
            if ($paschalFullMoon < 0) {
                $paschalFullMoon += 30;
            }
        } else { /* Gregorian Calendar */
            $dominicalNumber = ($year + ((int) ($year / 4)) - ((int) ($year / 100)) + ((int) ($year / 400))) % 7;
            if ($dominicalNumber < 0) {
                $dominicalNumber += 7;
            }

            /* the solar and lunar corrections */
            $solar = ((int) (($year - 1600) / 100)) - ((int) (($year - 1600) / 400));
            $lunar = (int) ((((int) (($year - 1400) / 100)) * 8) / 25);

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
     * Determine if Julian calendar should be used based on year and mode.
     *
     * @see https://github.com/php/php-src/blob/master/ext/calendar/easter.c
     */
    private static function useJulianCalendar(int $year, int $mode): bool
    {
        /*
         * From PHP source:
         * if ((year <= 1582 && method != CAL_EASTER_ALWAYS_GREGORIAN) ||
         *     (year >= 1583 && year <= 1752 && method != CAL_EASTER_ROMAN && method != CAL_EASTER_ALWAYS_GREGORIAN) ||
         *      method == CAL_EASTER_ALWAYS_JULIAN)
         */
        if ($mode === self::CAL_EASTER_ALWAYS_JULIAN) {
            return true;
        }

        if ($mode === self::CAL_EASTER_ALWAYS_GREGORIAN) {
            return false;
        }

        if ($mode === self::CAL_EASTER_ROMAN) {
            /* Julian for years <= 1582, Gregorian for years > 1582 */
            return $year <= 1582;
        }

        /* CAL_EASTER_DEFAULT: Julian for years <= 1752, Gregorian for years > 1752 */
        return $year <= 1752;
    }
}
