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
    public const NEW_MOON_OF_CREATION = 31524;

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
        2 => 13,
        5 => 13,
        7 => 13,
        10 => 13,
        13 => 13,
        16 => 13,
        18 => 13,
    ];

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
                $moladHalakim = $moladHalakim % self::HALAKIM_PER_DAY;
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
        int &$metonicCycle = null,
        int &$metonicYear = null,
        int &$moladDay = null,
        int &$moladHalakim = null,
        int &$tishri1 = null
    ): void {
        $metonicCycle = (int) (($year - 1) / 19);
        $metonicYear = ($year - 1) % 19;
        self::moladOfMetonicCycle($metonicCycle, $moladDay, $moladHalakim);

        $moladHalakim += self::HALAKIM_PER_LUNAR_CYCLE * self::$yearOffset[$metonicYear];
        $moladDay += (int) ($moladHalakim / self::HALAKIM_PER_DAY);
        $moladHalakim = $moladHalakim % self::HALAKIM_PER_DAY;

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

    private static function moladOfMetonicCycle(int $metonicCycle, &$moladDay = null, int &$moladHalakim = null): void
    {
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
}
