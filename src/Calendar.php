<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

/**
 * The calendar extension presents a series of functions to simplify converting
 * between different calendar formats. The intermediary or standard it is based
 * on is the Julian Day Count. The Julian Day Count is a count of days starting
 * from January 1st, 4713 B.C. To convert between calendar systems, you must
 * first convert to Julian Day Count, then to the calendar system of your
 * choice. Julian Day Count is very different from the Julian Calendar!
 *
 * @see https://www.php.net/manual/en/book.calendar.php
 */
final class Calendar
{
    public const CAL_GREGORIAN = 0;
    public const CAL_JULIAN = 1;
    public const CAL_JEWISH = 2;
    public const CAL_FRENCH = 3;
    public const CAL_NUM_CALS = 4;

    public const CAL_CONVERSION_TABLE = [
        self::CAL_GREGORIAN => Gregor::class,
        self::CAL_JULIAN => Julian::class,
        self::CAL_JEWISH => Jewish::class,
        self::CAL_FRENCH => French::class,
    ];

    public static function cal_to_jd(
        int $calendar,
        int $month,
        int $day,
        int $year
    ): int {
        if ($calendar < 0 || $calendar >= self::CAL_NUM_CALS) {
            throw new ValueError('Argument #1 ($calendar) must be a valid calendar ID');
        }

        /** @var SDNConversions $calendarType */
        $calendarType = self::CAL_CONVERSION_TABLE[$calendar];

        return $calendarType::toSDN($year, $month, $day);
    }

    /**
     * Return the number of days in a month for a given year and calendar.
     *
     * @see https://www.php.net/manual/en/function.cal-days-in-month.php
     */
    public static function cal_days_in_month(int $calendar, int $month, int $year): int
    {
        if ($calendar < 0 || $calendar >= self::CAL_NUM_CALS) {
            throw new ValueError('cal_days_in_month(): Argument #1 ($calendar) must be a valid calendar ID');
        }

        if ($year > 2147483645) {
            throw new ValueError('cal_days_in_month(): Argument #3 ($year) must be less than 2147483646');
        }

        if ($month < 1 || $month > 2147483645) {
            throw new ValueError('cal_days_in_month(): Argument #2 ($month) must be between 1 and 2147483646');
        }

        /** @var SDNConversions $calendarType */
        $calendarType = self::CAL_CONVERSION_TABLE[$calendar];

        /* Get SDN for first day of this month */
        $sdnStart = $calendarType::toSDN($year, $month, 1);

        /* Get SDN for first day of next month */
        $nextMonth = $month + 1;
        $nextYear = $year;

        /* Handle month overflow - go to first month of next year */
        $sdnNext = $calendarType::toSDN($nextYear, $nextMonth, 1);

        if ($sdnNext <= 0) {
            /* Next month is invalid, try first month of next year */
            $nextMonth = 1;
            $nextYear = $year + 1;

            /* Handle year 0 which doesn't exist in Gregorian/Julian */
            if ($nextYear === 0 && ($calendar === self::CAL_GREGORIAN || $calendar === self::CAL_JULIAN)) {
                $nextYear = 1;
            }

            $sdnNext = $calendarType::toSDN($nextYear, $nextMonth, 1);
        }

        /* If we still can't compute (e.g., end of French calendar), find last valid day by loop */
        if ($sdnNext <= 0) {
            for ($day = 1; $day <= 32; ++$day) {
                $sdnDay = $calendarType::toSDN($year, $month, $day);
                if ($sdnDay <= 0) {
                    return $day - 1;
                }
            }
        }

        /* If start is invalid, return 0 */
        if ($sdnStart <= 0) {
            return 0;
        }

        return $sdnNext - $sdnStart;
    }

    /**
     * Convert Julian Day to Unix timestamp.
     *
     * @see https://www.php.net/manual/en/function.jdtounix.php
     */
    public static function jdtounix(int $julian_day): int
    {
        $maxJd = self::getMaxJulianDay();

        /* Unix epoch starts at JD 2440588 */
        if ($julian_day < 2440588 || $julian_day > $maxJd) {
            throw new ValueError('jdtounix(): jday must be between 2440588 and ' . $maxJd);
        }

        return ($julian_day - 2440588) * 86400;
    }

    private static function getMaxJulianDay(): int
    {
        return \PHP_INT_SIZE === 8 ? 106751993607888 : 2465443;
    }
}
