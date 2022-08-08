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

    public function cal_to_jd(
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
}
