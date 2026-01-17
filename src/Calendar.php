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

    public const CAL_DOW_DAYNO = 0;
    public const CAL_DOW_LONG = 1;
    public const CAL_DOW_SHORT = 2;

    public const CAL_MONTH_GREGORIAN_SHORT = 0;
    public const CAL_MONTH_GREGORIAN_LONG = 1;
    public const CAL_MONTH_JULIAN_SHORT = 2;
    public const CAL_MONTH_JULIAN_LONG = 3;
    public const CAL_MONTH_JEWISH = 4;
    public const CAL_MONTH_FRENCH = 5;

    private const DAY_NAMES_LONG = [
        'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday',
    ];

    private const DAY_NAMES_SHORT = [
        'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat',
    ];

    private const MONTH_NAMES_SHORT = [
        '', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
    ];

    private const MONTH_NAMES_LONG = [
        '', 'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];

    private const FRENCH_MONTH_NAMES = [
        '', 'Vendemiaire', 'Brumaire', 'Frimaire', 'Nivose', 'Pluviose', 'Ventose',
        'Germinal', 'Floreal', 'Prairial', 'Messidor', 'Thermidor', 'Fructidor', 'Extra',
    ];

    private const JEWISH_MONTH_NAMES = [
        '', 'Tishri', 'Heshvan', 'Kislev', 'Tevet', 'Shevat', '',
        'Adar', 'Nisan', 'Iyyar', 'Sivan', 'Tammuz', 'Av', 'Elul',
    ];

    private const JEWISH_MONTH_NAMES_LEAP = [
        '', 'Tishri', 'Heshvan', 'Kislev', 'Tevet', 'Shevat', 'Adar I',
        'Adar II', 'Nisan', 'Iyyar', 'Sivan', 'Tammuz', 'Av', 'Elul',
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

    /**
     * Convert Unix timestamp to Julian Day.
     *
     * @see https://www.php.net/manual/en/function.unixtojd.php
     */
    public static function unixtojd(?int $timestamp = null): int
    {
        if ($timestamp === null) {
            $timestamp = time();
        }

        if ($timestamp < 0) {
            throw new ValueError('unixtojd(): Argument #1 ($timestamp) must be greater than or equal to 0');
        }

        return (int) ($timestamp / 86400) + 2440588;
    }

    /**
     * Returns the day of the week for a Julian Day.
     *
     * @see https://www.php.net/manual/en/function.jddayofweek.php
     *
     * @return int|string
     */
    public static function jddayofweek(int $julian_day, int $mode = self::CAL_DOW_DAYNO)
    {
        /* Calculate day of week: 0 = Sunday, 6 = Saturday */
        $dow = ($julian_day + 1) % 7;

        /* Handle negative Julian days */
        if ($dow < 0) {
            $dow += 7;
        }

        return match ($mode) {
            self::CAL_DOW_LONG => self::DAY_NAMES_LONG[$dow],
            self::CAL_DOW_SHORT => self::DAY_NAMES_SHORT[$dow],
            default => $dow,
        };
    }

    /**
     * Returns a string containing a month name.
     *
     * @see https://www.php.net/manual/en/function.jdmonthname.php
     */
    public static function jdmonthname(int $julian_day, int $mode): string
    {
        switch ($mode) {
            case self::CAL_MONTH_GREGORIAN_LONG:
                [$year, $month, $day] = Gregor::sdnToGregorian($julian_day);

                return self::MONTH_NAMES_LONG[$month] ?? '';

            case self::CAL_MONTH_JULIAN_SHORT:
                [$year, $month, $day] = Julian::sdnToJulian($julian_day);

                return self::MONTH_NAMES_SHORT[$month] ?? '';

            case self::CAL_MONTH_JULIAN_LONG:
                [$year, $month, $day] = Julian::sdnToJulian($julian_day);

                return self::MONTH_NAMES_LONG[$month] ?? '';

            case self::CAL_MONTH_JEWISH:
                [$year, $month, $day] = Jewish::sdnToJewish($julian_day);
                if ($year <= 0) {
                    return '';
                }

                /* A year is a leap year if (year * 7 + 1) % 19 < 7 */
                $isLeapYear = (($year * 7 + 1) % 19) < 7;
                $monthNames = $isLeapYear ? self::JEWISH_MONTH_NAMES_LEAP : self::JEWISH_MONTH_NAMES;

                return $monthNames[$month] ?? '';

            case self::CAL_MONTH_FRENCH:
                [$year, $month, $day] = French::sdnToFrench($julian_day);
                if ($year <= 0) {
                    return '';
                }

                return self::FRENCH_MONTH_NAMES[$month] ?? '';

            default:
            case self::CAL_MONTH_GREGORIAN_SHORT:
                [$year, $month, $day] = Gregor::sdnToGregorian($julian_day);

                return self::MONTH_NAMES_SHORT[$month] ?? '';
        }
    }

    /**
     * Converts from Julian Day Count to a supported calendar.
     *
     * @see https://www.php.net/manual/en/function.cal-from-jd.php
     *
     * @return array{date: string, month: int, day: int, year: int, dow: int|null, abbrevdayname: string, dayname: string, abbrevmonth: string, monthname: string}
     */
    public static function cal_from_jd(int $julian_day, int $calendar): array
    {
        if ($calendar < 0 || $calendar >= self::CAL_NUM_CALS) {
            throw new ValueError('cal_from_jd(): Argument #2 ($calendar) must be a valid calendar ID');
        }

        /* Get date components based on calendar type */
        switch ($calendar) {
            case self::CAL_GREGORIAN:
                [$year, $month, $day] = Gregor::sdnToGregorian($julian_day);
                $abbrevMonth = self::MONTH_NAMES_SHORT[$month] ?? '';
                $monthName = self::MONTH_NAMES_LONG[$month] ?? '';
                break;

            case self::CAL_JULIAN:
                [$year, $month, $day] = Julian::sdnToJulian($julian_day);
                $abbrevMonth = self::MONTH_NAMES_SHORT[$month] ?? '';
                $monthName = self::MONTH_NAMES_LONG[$month] ?? '';
                break;

            case self::CAL_JEWISH:
                [$year, $month, $day] = Jewish::sdnToJewish($julian_day);
                if ($year <= 0) {
                    /* Bug #71894: Jewish calendar with year 0 returns null dow */
                    return [
                        'date' => '0/0/0',
                        'month' => 0,
                        'day' => 0,
                        'year' => 0,
                        'dow' => null,
                        'abbrevdayname' => '',
                        'dayname' => '',
                        'abbrevmonth' => '',
                        'monthname' => '',
                    ];
                }
                $isLeapYear = (($year * 7 + 1) % 19) < 7;
                $monthNames = $isLeapYear ? self::JEWISH_MONTH_NAMES_LEAP : self::JEWISH_MONTH_NAMES;
                $abbrevMonth = $monthNames[$month] ?? '';
                $monthName = $monthNames[$month] ?? '';
                break;

            case self::CAL_FRENCH:
                [$year, $month, $day] = French::sdnToFrench($julian_day);
                $abbrevMonth = self::FRENCH_MONTH_NAMES[$month] ?? '';
                $monthName = self::FRENCH_MONTH_NAMES[$month] ?? '';
                break;
        }

        /* Calculate day of week using existing method */
        $dow = self::jddayofweek($julian_day, self::CAL_DOW_DAYNO);
        $abbrevDayName = self::jddayofweek($julian_day, self::CAL_DOW_SHORT);
        $dayName = self::jddayofweek($julian_day, self::CAL_DOW_LONG);

        return [
            'date' => "{$month}/{$day}/{$year}",
            'month' => $month,
            'day' => $day,
            'year' => $year,
            'dow' => $dow,
            'abbrevdayname' => $abbrevDayName,
            'dayname' => $dayName,
            'abbrevmonth' => $abbrevMonth,
            'monthname' => $monthName,
        ];
    }

    private static function getMaxJulianDay(): int
    {
        return \PHP_INT_SIZE === 8 ? 106751993607888 : 2465443;
    }
}
