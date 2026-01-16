<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

/**
 * Jewish Serial Day Number (SDN) conversions
 *
 * @see https://github.com/php/php-src/blob/PHP-8.1.9/ext/calendar/jewish.c
 */
final class Jewish implements SDNConversions
{
    public const HALAKIM_PER_HOUR = 1080;
    public const HALAKIM_PER_DAY = 25920;
    public const HALAKIM_PER_LUNAR_CYCLE = (29 * self::HALAKIM_PER_DAY) + 13753;
    public const HALAKIM_PER_METONIC_CYCLE = self::HALAKIM_PER_LUNAR_CYCLE * (12 * 19 + 7);

    public const JEWISH_SDN_OFFSET = 347997;
    public const JEWISH_SDN_MAX = 324542846;
    public const NEW_MOON_OF_CREATION = 31524;

    public const CAL_JEWISH_ADD_ALAFIM_GERESH = 2;
    public const CAL_JEWISH_ADD_ALAFIM = 4;
    public const CAL_JEWISH_ADD_GERESHAYIM = 8;

    public const SUNDAY = 0;
    public const MONDAY = 1;
    public const TUESDAY = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY = 4;
    public const FRIDAY = 5;
    public const SATURDAY = 6;

    public const NOON = 18 * self::HALAKIM_PER_HOUR;
    public const AM3_11_20 = (9 * self::HALAKIM_PER_HOUR) + 204;
    public const AM9_32_43 = (15 * self::HALAKIM_PER_HOUR) + 589;

    private static $yearOffset = [
        0, 12, 24, 37, 49, 61, 74, 86, 99, 111, 123,
        136, 148, 160, 173, 185, 197, 210, 222,
    ];

    private static $monthsPerYear = [
        12, 12, 13, 12, 12, 13, 12, 13, 12, 12, 13, 12, 12, 13, 12, 12, 13, 12, 13,
    ];

    /** @var string[] Hebrew month names for regular years (14 entries: 0-13) */
    private static array $jewishMonthName = [
        '',                     /* 0 - unused */
        "\xFA\xF9\xF8\xE9",     /* 1 - Tishri */
        "\xE7\xF9\xE5\xEF",     /* 2 - Heshvan */
        "\xEB\xF1\xEC\xE5",     /* 3 - Kislev */
        "\xE8\xE1\xFA",         /* 4 - Tevet */
        "\xF9\xE1\xE8",         /* 5 - Shevat */
        '',                     /* 6 - Adar I (not used in regular years) */
        "\xE0\xE3\xF8",         /* 7 - Adar */
        "\xF0\xE9\xF1\xEF",     /* 8 - Nisan */
        "\xE0\xE9\xE9\xF8",     /* 9 - Iyyar */
        "\xF1\xE9\xE5\xEF",     /* 10 - Sivan */
        "\xFA\xEE\xE5\xE6",     /* 11 - Tammuz */
        "\xE0\xE1",             /* 12 - Av */
        "\xE0\xEC\xE5\xEC",     /* 13 - Elul */
    ];

    /** @var string[] Hebrew month names for leap years (14 entries: 0-13) */
    private static array $jewishMonthNameLeap = [
        '',                     /* 0 - unused */
        "\xFA\xF9\xF8\xE9",     /* 1 - Tishri */
        "\xE7\xF9\xE5\xEF",     /* 2 - Heshvan */
        "\xEB\xF1\xEC\xE5",     /* 3 - Kislev */
        "\xE8\xE1\xFA",         /* 4 - Tevet */
        "\xF9\xE1\xE8",         /* 5 - Shevat */
        "\xE0\xE3\xF8 \xE0'",   /* 6 - Adar I */
        "\xE0\xE3\xF8 \xE1'",   /* 7 - Adar II */
        "\xF0\xE9\xF1\xEF",     /* 8 - Nisan */
        "\xE0\xE9\xE9\xF8",     /* 9 - Iyyar */
        "\xF1\xE9\xE5\xEF",     /* 10 - Sivan */
        "\xFA\xEE\xE5\xE6",     /* 11 - Tammuz */
        "\xE0\xE1",             /* 12 - Av */
        "\xE0\xEC\xE5\xEC",     /* 13 - Elul */
    ];

    /**
     * Hebrew alphabet for number conversion (ISO-8859-8).
     *
     * Index 0 is unused, indices 1-9 are alef-tet (1-9),
     * index 10 is yud (10), indices 11-18 are kaf-tzadi (20-90),
     * indices 19-22 are kuf-tav (100-400).
     */
    private static array $alefBet = [
        '0',    /* 0 - unused */
        "\xE0", /* 1 - alef = 1 */
        "\xE1", /* 2 - bet = 2 */
        "\xE2", /* 3 - gimel = 3 */
        "\xE3", /* 4 - dalet = 4 */
        "\xE4", /* 5 - he = 5 */
        "\xE5", /* 6 - vav = 6 */
        "\xE6", /* 7 - zayin = 7 */
        "\xE7", /* 8 - chet = 8 */
        "\xE8", /* 9 - tet = 9 */
        "\xE9", /* 10 - yud = 10 */
        "\xEB", /* 11 - kaf = 20 */
        "\xEC", /* 12 - lamed = 30 */
        "\xEE", /* 13 - mem = 40 */
        "\xF0", /* 14 - nun = 50 */
        "\xF1", /* 15 - samech = 60 */
        "\xF2", /* 16 - ayin = 70 */
        "\xF4", /* 17 - pe = 80 */
        "\xF6", /* 18 - tzadi = 90 */
        "\xF7", /* 19 - kuf = 100 */
        "\xF8", /* 20 - reish = 200 */
        "\xF9", /* 21 - shin = 300 */
        "\xFA", /* 22 - tav = 400 */
    ];

    /**
     * Converts a Julian Day Count to a Jewish Calendar date.
     *
     * @see https://www.php.net/manual/en/function.jdtojewish.php
     */
    public static function jdtojewish(int $julianDay, bool $hebrew = false, int $flags = 0): string
    {
        [$year, $month, $day] = self::sdnToJewish($julianDay);

        if (!$hebrew) {
            return "{$month}/{$day}/{$year}";
        }

        if ($year <= 0 || $year > 9999) {
            throw new ValueError('Year out of range (0-9999)');
        }

        $monthNames = self::getMonthNames($year);
        $dayStr = self::hebNumberToChars($day, $flags);
        $yearStr = self::hebNumberToChars($year, $flags);

        return "{$dayStr} {$monthNames[$month]} {$yearStr}";
    }

    /**
     * Converts a SDN to Jewish year, month, day.
     *
     * @return array{int, int, int} [year, month, day]
     */
    public static function sdnToJewish(int $sdn): array
    {
        if ($sdn <= self::JEWISH_SDN_OFFSET || $sdn > self::JEWISH_SDN_MAX) {
            return [0, 0, 0];
        }

        $inputDay = $sdn - self::JEWISH_SDN_OFFSET;

        self::findTishriMolad($inputDay, $metonicCycle, $metonicYear, $moladDay, $moladHalakim);
        $tishri1 = self::tishri1($metonicYear, $moladDay, $moladHalakim);

        if ($inputDay >= $tishri1) {
            /* This day is on or after the start of the year. */
            $year = $metonicCycle * 19 + $metonicYear + 1;

            if ($inputDay < $tishri1 + 59) {
                if ($inputDay < $tishri1 + 30) {
                    return [$year, 1, $inputDay - $tishri1 + 1];
                }

                return [$year, 2, $inputDay - $tishri1 - 29];
            }

            /* Calculate the next year Tishri 1. */
            $moladHalakim += self::HALAKIM_PER_LUNAR_CYCLE * self::$monthsPerYear[$metonicYear];
            $moladDay += (int) ($moladHalakim / self::HALAKIM_PER_DAY);
            $moladHalakim %= self::HALAKIM_PER_DAY;
            $tishri1After = self::tishri1(($metonicYear + 1) % 19, $moladDay, $moladHalakim);
        } else {
            /* This day is before the start of the year. */
            $year = $metonicCycle * 19 + $metonicYear;

            if ($inputDay >= $tishri1 - 177) {
                /* This day is one of the last 6 months of the year. */
                if ($inputDay > $tishri1 - 30) {
                    return [$year, 13, $inputDay - $tishri1 + 30];
                }
                if ($inputDay > $tishri1 - 60) {
                    return [$year, 12, $inputDay - $tishri1 + 60];
                }
                if ($inputDay > $tishri1 - 89) {
                    return [$year, 11, $inputDay - $tishri1 + 89];
                }
                if ($inputDay > $tishri1 - 119) {
                    return [$year, 10, $inputDay - $tishri1 + 119];
                }
                if ($inputDay > $tishri1 - 148) {
                    return [$year, 9, $inputDay - $tishri1 + 148];
                }

                return [$year, 8, $inputDay - $tishri1 + 178];
            }

            if (self::$monthsPerYear[($year - 1) % 19] === 13) {
                $month = 7;
                $day = $inputDay - $tishri1 + 207;
                if ($day > 0) {
                    return [$year, $month, $day];
                }
                --$month;
                $day += 30;
                if ($day > 0) {
                    return [$year, $month, $day];
                }
                --$month;
                $day += 30;
            } else {
                $month = 7;
                $day = $inputDay - $tishri1 + 207;
                if ($day > 0) {
                    return [$year, $month, $day];
                }
                $month -= 2;
                $day += 30;
            }

            if ($day > 0) {
                return [$year, $month, $day];
            }
            --$month;
            $day += 29;
            if ($day > 0) {
                return [$year, $month, $day];
            }

            /* We need the length of the year to figure out the month and day. */
            $tishri1After = $tishri1;
            self::findTishriMolad($moladDay - 365, $metonicCycle, $metonicYear, $moladDay, $moladHalakim);
            $tishri1 = self::tishri1($metonicYear, $moladDay, $moladHalakim);
        }

        $yearLength = $tishri1After - $tishri1;
        $day = $inputDay - $tishri1 - 29;

        if ($yearLength === 355 || $yearLength === 385) {
            if ($day <= 30) {
                return [$year, 2, $day];
            }
            $day -= 30;
        } else {
            if ($day <= 29) {
                return [$year, 2, $day];
            }
            $day -= 29;
        }

        return [$year, 3, $day];
    }

    /**
     * @author Scott E. Lee
     * @see https://github.com/php/php-src/blob/5b01c4863fe9e4bc2702b2bbf66d292d23001a18/ext/calendar/jewish.c
     */
    public static function jewishtojd(int $month, int $day, int $year): int
    {
        if ($year <= 0 || $day <= 0 || $day > 30) {
            return 0;
        }

        switch ($month) {
            case 1:
            case 2:
                /* It is Tishri or Heshvan - don't need the year length. */
                self::findStartOfYear($year, $metonicCycle, $metonicYear, $moladDay, $moladHalakim, $tishri1);
                $sdn = $tishri1 + $day + ($month === 1 ? -1 : 29);
                break;

            case 3:
                /* It is Kislev - must find the year length. */

                /* Find the start of the year. */
                self::findStartOfYear($year, $metonicCycle, $metonicYear, $moladDay, $moladHalakim, $tishri1);

                /* Find the end of the year. */
                $moladHalakim += self::HALAKIM_PER_LUNAR_CYCLE * self::getMonthsInYear($metonicYear);
                $moladDay += (int) ($moladHalakim / self::HALAKIM_PER_DAY);
                $moladHalakim %= self::HALAKIM_PER_DAY;
                $tishri1After = self::tishri1(($metonicYear + 1) % 19, $moladDay, $moladHalakim);

                $yearLength = $tishri1After - $tishri1;

                $sdn = $tishri1 + $day + ($yearLength == 355 || $yearLength == 385 ? 59 : 58);
                break;

            case 4:
            case 5:
            case 6:
                /* It is Tevet, Shevat or Adar I - don't need the year length. */

                self::findStartOfYear($year + 1, $metonicCycle, $metonicYear, $moladDay, $moladHalakim, $tishri1After);

                $lengthOfAdarIAndII = (self::getMonthsInYear(($year - 1) % 19) == 12) ? 29 : 59;

                $sdn = self::calSDN($month, (int) $tishri1After, $day, $lengthOfAdarIAndII);
                break;

            default:
                /* It is Adar II or later - don't need the year length. */
                self::findStartOfYear($year + 1, $metonicCycle, $metonicYear, $moladDay, $moladHalakim, $tishri1After);

                switch ($month) {
                    case 7:
                        $sdn = $tishri1After + $day - 207;
                        break;
                    case 8:
                        $sdn = $tishri1After + $day - 178;
                        break;
                    case 9:
                        $sdn = $tishri1After + $day - 148;
                        break;
                    case 10:
                        $sdn = $tishri1After + $day - 119;
                        break;
                    case 11:
                        $sdn = $tishri1After + $day - 89;
                        break;
                    case 12:
                        $sdn = $tishri1After + $day - 60;
                        break;
                    case 13:
                        $sdn = $tishri1After + $day - 30;
                        break;
                    default:
                        return 0;
                }
        }

        return $sdn + self::JEWISH_SDN_OFFSET;
    }

    /**
     * Convert a Jewish republican calendar date to a SDN.
     * {@inheritDoc}
     */
    public static function toSDN(int $year, int $month, int $day): int
    {
        $metonicCycle = 0;
        $metonicYear = 0;
        $tishri1 = 0;
        $tishri1After = 0;
        $moladDay = 0;
        $moladHalakim = 0;

        if ($year <= 0 || $day <= 0 || $day > 30) {
            return 0;
        }
        switch ($month) {
            case 1:
            case 2:
                /* It is Tishri or Heshvan - don't need the $year length. */
                self::findStartOfYear($year, $metonicCycle, $metonicYear, $moladDay, $moladHalakim, $tishri1);
                if ($month == 1) {
                    $sdn = $tishri1 + $day - 1;
                } else {
                    $sdn = $tishri1 + $day + 29;
                }
                break;

            case 3:
                /* It is Kislev - must find the $year length. */

                /* Find the start of the $year. */
                self::findStartOfYear($year, $metonicCycle, $metonicYear, $moladDay, $moladHalakim, ${$tishri1});

                /* Find the end of the $year. */
                $moladHalakim += self::HALAKIM_PER_LUNAR_CYCLE * self::getMonthsInYear($metonicYear);
                $moladDay += (int) ($moladHalakim / self::HALAKIM_PER_DAY);
                $moladHalakim %= self::HALAKIM_PER_DAY;
                $tishri1After = self::tishri1(($metonicYear + 1) % 19, $moladDay, $moladHalakim);

                $yearLength = $tishri1After - $tishri1;

                if ($yearLength === 355 || $yearLength === 385) {
                    $sdn = $tishri1 + $day + 59;
                } else {
                    $sdn = $tishri1 + $day + 58;
                }
                break;

            case 4:
            case 5:
            case 6:
                /* It is Tevet, Shevat or Adar I - don't need the $year length. */

                self::findStartOfYear($year + 1, $metonicCycle, $metonicYear, $moladDay, $moladHalakim, $tishri1After);

                if (self::getMonthsInYear(($year - 1) % 19) === 12) {
                    $lengthOfAdarIAndII = 29;
                } else {
                    $lengthOfAdarIAndII = 59;
                }

                $sdn = self::calSDN($month, (int) $tishri1After, $day, $lengthOfAdarIAndII);
                break;

            default:
                /* It is Adar II or later - don't need the $year length. */
                self::findStartOfYear($year + 1, $metonicCycle, $metonicYear, $moladDay, $moladHalakim, $tishri1After);

                switch ($month) {
                    case 7:
                        $sdn = $tishri1After + $day - 207;
                        break;
                    case 8:
                        $sdn = $tishri1After + $day - 178;
                        break;
                    case 9:
                        $sdn = $tishri1After + $day - 148;
                        break;
                    case 10:
                        $sdn = $tishri1After + $day - 119;
                        break;
                    case 11:
                        $sdn = $tishri1After + $day - 89;
                        break;
                    case 12:
                        $sdn = $tishri1After + $day - 60;
                        break;
                    case 13:
                        $sdn = $tishri1After + $day - 30;
                        break;
                    default:
                        return 0;
                }
        }

        return $sdn + self::JEWISH_SDN_OFFSET;
    }

    private static function calSDN(
        int $month,
        int $tishri1After,
        int $day,
        int $lengthOfAdarIAndII
    ): int {
        if ($month === 4) {
            $sdn = $tishri1After + $day - $lengthOfAdarIAndII - 237;
        } elseif ($month === 5) {
            $sdn = $tishri1After + $day - $lengthOfAdarIAndII - 208;
        } else {
            $sdn = $tishri1After + $day - $lengthOfAdarIAndII - 178;
        }

        return $sdn;
    }

    private static function getMonthsInYear($metonicYear): int
    {
        return self::$monthsPerYear[(int) $metonicYear] ?? 12;
    }

    private static function findStartOfYear(
        int $year,
        ?int &$metonicCycle = null,
        ?int &$metonicYear = null,
        ?int &$moladDay = null,
        ?int &$moladHalakim = null,
        ?int &$tishri1 = null
    ): void {
        $metonicCycle = (int) (($year - 1) / 19);
        $metonicYear = ($year - 1) % 19;
        self::moladOfMetonicCycle($metonicCycle, $moladDay, $moladHalakim);

        $moladHalakim += self::HALAKIM_PER_LUNAR_CYCLE * self::$yearOffset[$metonicYear];
        $moladDay += (int) ($moladHalakim / self::HALAKIM_PER_DAY);
        $moladHalakim %= self::HALAKIM_PER_DAY;

        $tishri1 = self::tishri1($metonicYear, $moladDay, $moladHalakim);
    }

    private static function tishri1(int $metonicYear, int $moladDay, int $moladHalakim): int
    {
        $tishri1 = $moladDay;
        $dow = $tishri1 % 7;
        $leapYear = $metonicYear === 2 || $metonicYear === 5 || $metonicYear === 7
            || $metonicYear === 10
            || $metonicYear === 13
            || $metonicYear === 16
            || $metonicYear === 18;
        $lastWasLeapYear = $metonicYear === 3 || $metonicYear === 6
            || $metonicYear === 8
            || $metonicYear === 11
            || $metonicYear === 14
            || $metonicYear === 17
            || $metonicYear === 0;

        /* Apply rules 2, 3 and 4. */
        if (($moladHalakim >= self::NOON)
            || ((!$leapYear) && $dow === self::TUESDAY && $moladHalakim >= self::AM3_11_20)
            || ($lastWasLeapYear && $dow === self::MONDAY && $moladHalakim >= self::AM9_32_43)
        ) {
            ++$tishri1;
            ++$dow;

            if ($dow === 7) {
                $dow = 0;
            }
        }

        /* Apply rule 1 after the others because it can cause an additional
         * delay of one day. */
        if ($dow === self::WEDNESDAY || $dow === self::FRIDAY || $dow === self::SUNDAY) {
            ++$tishri1;
        }

        return $tishri1;
    }

    private static function moladOfMetonicCycle(
        int $metonicCycle,
        ?int &$moladDay = null,
        ?int &$moladHalakim = null
    ): void {
        /* Start with the time of the first molad after creation. */
        $r1 = self::NEW_MOON_OF_CREATION;

        /* Calculate metonicCycle * HALAKIM_PER_METONIC_CYCLE.  The upper 32
         * bits of the result will be in r2 and the lower 16 bits will be
         * in r1. */
        $r1 += $metonicCycle * (self::HALAKIM_PER_METONIC_CYCLE & 0xFFFF);
        $r2 = $r1 >> 16;
        $r2 += $metonicCycle * ((self::HALAKIM_PER_METONIC_CYCLE >> 16) & 0xFFFF);

        /* Calculate r2r1 / HALAKIM_PER_DAY.  The remainder will be in r1, the
         * upper 16 bits of the quotient will be in d2 and the lower 16 bits
         * will be in d1. */
        $d2 = (int) ($r2 / self::HALAKIM_PER_DAY);
        $r2 -= $d2 * self::HALAKIM_PER_DAY;
        $r1 = ($r2 << 16) | ($r1 & 0xFFFF);
        $d1 = (int) ($r1 / self::HALAKIM_PER_DAY);
        $r1 -= $d1 * self::HALAKIM_PER_DAY;

        $moladDay = ($d2 << 16) | $d1;
        $moladHalakim = $r1;
    }

    /**
     * Find the molad of Tishri closest to the given input day.
     *
     * It's not really the *closest* molad - if the input day is in the first two
     * months, we want the molad at the start of the year. If the input day is
     * in the fourth to last months, we want the molad at the end of the year.
     * If the input day is in the third month, it doesn't matter which molad is
     * returned, because both will be required.
     *
     * @see https://github.com/php/php-src/blob/PHP-8.2/ext/calendar/jewish.c
     */
    private static function findTishriMolad(
        int $inputDay,
        ?int &$metonicCycle = null,
        ?int &$metonicYear = null,
        ?int &$moladDay = null,
        ?int &$moladHalakim = null
    ): void {
        /* Estimate the metonic cycle number. Note that this may be an under
         * estimate because there are 6939.6896 days in a metonic cycle not
         * 6940, but it will never be an over estimate. The loop below will
         * correct for any error in this estimate. */
        $metonicCycle = (int) (($inputDay + 310) / 6940);

        /* Calculate the time of the starting molad for this metonic cycle. */
        self::moladOfMetonicCycle($metonicCycle, $moladDay, $moladHalakim);

        /* If the above was an under estimate, increment the cycle number until
         * the correct one is found. */
        while ($moladDay < $inputDay - 6940 + 310) {
            ++$metonicCycle;
            $moladHalakim += self::HALAKIM_PER_METONIC_CYCLE;
            $moladDay += (int) ($moladHalakim / self::HALAKIM_PER_DAY);
            $moladHalakim %= self::HALAKIM_PER_DAY;
        }

        /* Find the molad of Tishri closest to this date. */
        for ($metonicYear = 0; $metonicYear < 18; ++$metonicYear) {
            if ($moladDay > $inputDay - 74) {
                break;
            }
            $moladHalakim += self::HALAKIM_PER_LUNAR_CYCLE * self::$monthsPerYear[$metonicYear];
            $moladDay += (int) ($moladHalakim / self::HALAKIM_PER_DAY);
            $moladHalakim %= self::HALAKIM_PER_DAY;
        }
    }

    /**
     * Returns the appropriate month names array based on whether the year is a leap year.
     *
     * @return string[]
     */
    private static function getMonthNames(int $year): array
    {
        /* A year is a leap year if (year * 7 + 1) % 19 < 7 */
        $isLeapYear = (($year * 7 + 1) % 19) < 7;

        return $isLeapYear ? self::$jewishMonthNameLeap : self::$jewishMonthName;
    }

    /**
     * Converts a number to Hebrew characters (ISO-8859-8 encoding).
     *
     * @see https://github.com/php/php-src/blob/PHP-8.2/ext/calendar/calendar.c
     */
    private static function hebNumberToChars(int $n, int $flags): ?string
    {
        if ($n > 9999 || $n < 1) {
            return null;
        }

        $buf = '';
        $endOfAlafim = 0;

        /* alafim (thousands) case */
        if ((int) ($n / 1000)) {
            $buf .= self::$alefBet[(int) ($n / 1000)];

            if ($flags & self::CAL_JEWISH_ADD_ALAFIM_GERESH) {
                $buf .= "'";
            }
            if ($flags & self::CAL_JEWISH_ADD_ALAFIM) {
                $buf .= " \xE0\xEC\xF4\xE9\xED "; /* " alafim " */
            }

            $endOfAlafim = mb_strlen($buf, '8bit');
            $n %= 1000;
        }

        /* tav-tav (tav=400) case */
        while ($n >= 400) {
            $buf .= self::$alefBet[22]; /* tav */
            $n -= 400;
        }

        /* meot (hundreds) case */
        if ($n >= 100) {
            $buf .= self::$alefBet[18 + (int) ($n / 100)];
            $n %= 100;
        }

        /* tet-vav & tet-zain case (special case for 15 and 16) */
        if ($n === 15 || $n === 16) {
            $buf .= self::$alefBet[9]; /* tet */
            $buf .= self::$alefBet[$n - 9];
        } else {
            /* asarot (tens) case */
            if ($n >= 10) {
                $buf .= self::$alefBet[9 + (int) ($n / 10)];
                $n %= 10;
            }

            /* yehidot (ones) case */
            if ($n > 0) {
                $buf .= self::$alefBet[$n];
            }
        }

        if ($flags & self::CAL_JEWISH_ADD_GERESHAYIM) {
            $len = mb_strlen($buf, '8bit');
            $afterAlafim = $len - $endOfAlafim;

            if ($afterAlafim === 0) {
                /* nothing after alafim */
            } elseif ($afterAlafim === 1) {
                /* single character, add geresh */
                $buf .= "'";
            } else {
                /* insert gereshayim before last character */
                $buf = mb_substr($buf, 0, $len - 1, '8bit') . '"' . mb_substr($buf, $len - 1, 1, '8bit');
            }
        }

        return $buf;
    }
}
