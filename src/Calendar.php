<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

class Calendar
{
    private const MARCH = 3;
    private const APRIL = 4;

    /**
     * Return the timestamp of midnight on Easter of a given year (defaults to current year)
     *
     * Based on code by Simon Kershaw <simon@oremus.org>
     * @see: http://easter.oremus.org/when/bradley.html
     */
    public static function easter_date(int $year = null): int
    {
        if (!$year) {
            $year = (int) date('Y');
        }

        /* out of range for timestamps */
        if ($year < 1970 || $year > 2037) {
            throw new ValueError('This function is only valid for years between 1970 and 2037 inclusive');
        }

        /* the Golden number */
        $golden = ($year % 19) + 1;

        /* the solar and lunar corrections */
        $solar = ($year - 1600) / 100 - ($year - 1600) / 400;
        $lunar = ((($year - 1400) / 100) * 8) / 25;

        /* uncorrected date of the Paschal full moon */
        $paschalFullMoon = (3 - (11 * $golden) + $solar - $lunar) % 30;
        if ($paschalFullMoon < 0) {
            $paschalFullMoon += 30;
        }

        /* corrected date of the Paschal full moon */
        if (($paschalFullMoon == 29) || ($paschalFullMoon == 28 && $golden > 11)) {
            /* days after 21st March */
            --$paschalFullMoon;
        }

        /* the "Dominical number" */
        $dominicalNumber = ($year + ($year / 4) - ($year / 100) + ($year / 400)) % 7;
        if ($dominicalNumber < 0) {
            $dominicalNumber += 7;
        }

        $paschalFullMoonPrime = (4 - $paschalFullMoon - $dominicalNumber) % 7;
        if ($paschalFullMoonPrime < 0) {
            $paschalFullMoonPrime += 7;
        }

        /* Easter as the number of days after 21st March */
        $easter = $paschalFullMoon + $paschalFullMoonPrime + 1;

        if ($easter < 11) {
            $month = self::MARCH;
            $day = $easter + 21;
        } else {
            $month = self::APRIL;
            $day = $easter - 10;
        }

        return mktime(0, 0, 0, $month, $day, $year);
    }
}
