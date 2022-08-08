<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

final class Jewish
{
    const HALAKIM_PER_HOUR = 1080;
    const HALAKIM_PER_DAY = 25920;
    const HALAKIM_PER_LUNAR_CYCLE = (29 * self::HALAKIM_PER_DAY) + 13753;
    const HALAKIM_PER_METONIC_CYCLE = self::HALAKIM_PER_LUNAR_CYCLE * (12 * 19 + 7);

    const JEWISH_SDN_OFFSET = 347997;
    const NEW_MOON_OF_CREATION = 31524;

    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    const NOON = 18 * self::HALAKIM_PER_HOUR;
    const AM3_11_20 = (9 * self::HALAKIM_PER_HOUR) + 204;
    const AM9_32_43 = (15 * self::HALAKIM_PER_HOUR) + 589;

    private static $yearOffset = [
        0, 12, 24, 37, 49, 61, 74, 86, 99, 111, 123,
        136, 148, 160, 173, 185, 197, 210, 222,
    ];

    private static $monthsPerYear = [
        12, 12, 13, 12, 12, 13, 12, 13, 12, 12,
        13, 12, 12, 13, 12, 12, 13, 12, 13,
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
                self::findStartOfYear($year, $metonicCycle, $metonicYear,$moladDay, $moladHalakim, $tishri1);

                /* Find the end of the year. */
                $moladHalakim += self::HALAKIM_PER_LUNAR_CYCLE * self::$monthsPerYear[$metonicYear];
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

            $lengthOfAdarIAndII = (self::$monthsPerYear[($year - 1) % 19] == 12) ? 29 : 59;

                if ($month === 4) {
                    $sdn = $tishri1After + $day - $lengthOfAdarIAndII - 237;
                } else if ($month === 5) {
                    $sdn = $tishri1After + $day - $lengthOfAdarIAndII - 208;
                } else {
                    $sdn = $tishri1After + $day - $lengthOfAdarIAndII - 178;
                }
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

        return ($sdn +self::JEWISH_SDN_OFFSET);
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
            || $metonicYear === 10 || $metonicYear === 13 || $metonicYear === 16
            || $metonicYear === 18;
        $lastWasLeapYear = $metonicYear === 3 || $metonicYear === 6
            || $metonicYear === 8 || $metonicYear === 11 || $metonicYear === 14
            || $metonicYear === 17 || $metonicYear === 0;

        /* Apply rules 2, 3 and 4. */
        if (($moladHalakim >= self::NOON) ||
            ((!$leapYear) && $dow === self::TUESDAY && $moladHalakim >= self::AM3_11_20) ||
            ($lastWasLeapYear && $dow === self::MONDAY && $moladHalakim >= self::AM9_32_43)
        ) {
            $tishri1++;
            $dow++;

            if ($dow === 7) {
                $dow = 0;
            }
        }

        /* Apply rule 1 after the others because it can cause an additional
         * delay of one day. */
        if ($dow === self::WEDNESDAY || $dow === self::FRIDAY || $dow === self::SUNDAY) {
            $tishri1++;
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
