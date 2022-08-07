<?php

declare(strict_types=1);

namespace Roukmoute\Polyfill\Calendar;

final class Julian
{
    public static function jdtogregorian(int $julian): string
    {
        $julian = $julian - 1721119;
        $calc1 = 4 * $julian - 1;
        $year = floor($calc1 / 146097);
        $julian = floor($calc1 - 146097 * $year);
        $day = floor($julian / 4);
        $calc2 = 4 * $day + 3;
        $julian = floor($calc2 / 1461);
        $day = $calc2 - 1461 * $julian;
        $day = floor(($day + 4) / 4);
        $calc3 = 5 * $day - 3;
        $month = floor($calc3 / 153);
        $day = $calc3 - 153 * $month;
        $day = floor(($day + 5) / 5);
        $year = 100 * $year + $julian;

        if ($month < 10) {
            $month = $month + 3;
        } else {
            $month = $month - 9;
            $year = $year + 1;
        }

        return "$month/$day/$year";
    }
}
