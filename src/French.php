<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

/**
 * French Serial Day Number (SDN) conversions
 *
 * @see https://github.com/php/php-src/blob/PHP-8.1.9/ext/calendar/french.c
 */
class French implements SDNConversions
{
    private const FRENCH_SDN_OFFSET = 2375474;
    private const DAYS_PER_4_YEARS = 1461;
    private const DAYS_PER_MONTH = 30;

    /**
     * Convert a French republican calendar date to a SDN.
     * {@inheritDoc}
     */
    public static function toSDN(int $year, int $month, int $day): int
    {
        /* check for invalid dates */
        if ($year < 1 || $year > 14
            || $month < 1
            || $month > 13
            || $day < 1
            || $day > 30) {
            return 0;
        }

        /* Month 13 (Sansculottides) has only 5 or 6 days */
        if ($month === 13) {
            /* Leap years: 3, 7, 11 have 6 days, others have 5 */
            $maxDay = ($year === 3 || $year === 7 || $year === 11) ? 6 : 5;
            if ($day > $maxDay) {
                return 0;
            }
        }

        return (int) (($year * self::DAYS_PER_4_YEARS) / 4)
            + ($month - 1) * self::DAYS_PER_MONTH
            + $day
            + self::FRENCH_SDN_OFFSET;
    }
}
