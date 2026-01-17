<?php

declare(strict_types=1);

namespace Spec\Roukmoute\Polyfill\Calendar;

use PhpSpec\ObjectBehavior;
use Roukmoute\Polyfill\Calendar\Easter;
use Roukmoute\Polyfill\Calendar\ValueError;

class EasterSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Easter::class);
    }

    public function it_calculates_easter_date_for_year_2000(): void
    {
        $this->easter_date(2000)->shouldReturn(956448000);
    }

    public function it_calculates_easter_date_for_year_2001(): void
    {
        $this->easter_date(2001)->shouldReturn(987292800);
    }

    public function it_calculates_easter_date_for_year_2002(): void
    {
        $this->easter_date(2002)->shouldReturn(1017532800);
    }

    public function it_throws_an_exception_when_year_is_before_1970(): void
    {
        $this->shouldThrow(ValueError::class)->during('easter_date', [1969]);
    }

    public function it_throws_an_exception_when_year_is_after_2037(): void
    {
        $this->shouldThrow(ValueError::class)->during('easter_date', [2038]);
    }

    public function it_calculates_easter_days_for_year_1999(): void
    {
        $this->easter_days(1999)->shouldReturn(14);
    }

    public function it_calculates_easter_days_for_year_1492(): void
    {
        $this->easter_days(1492)->shouldReturn(32);
    }

    public function it_calculates_easter_days_for_year_1913(): void
    {
        $this->easter_days(1913)->shouldReturn(2);
    }

    public function it_calculates_easter_days_for_year_2025(): void
    {
        $this->easter_days(2025)->shouldReturn(30);
    }

    /**
     * Test easter_days with CAL_EASTER_ALWAYS_JULIAN mode.
     * For year 2000, Julian calendar gives a different result than Gregorian.
     */
    public function it_calculates_easter_days_with_always_julian_mode(): void
    {
        /* Year 2000: Gregorian Easter = April 23 (33 days after March 21) */
        $this->easter_days(2000, Easter::CAL_EASTER_DEFAULT)->shouldReturn(33);

        /* Year 2000: Julian Easter = April 17 (27 days after March 21) */
        $this->easter_days(2000, Easter::CAL_EASTER_ALWAYS_JULIAN)->shouldReturn(27);
    }

    /**
     * Test easter_days with CAL_EASTER_ALWAYS_GREGORIAN mode.
     * For year 1500, Julian calendar is normally used with DEFAULT mode.
     */
    public function it_calculates_easter_days_with_always_gregorian_mode(): void
    {
        /* Year 1500: With DEFAULT mode, uses Julian calendar */
        $julianResult = $this->easter_days(1500, Easter::CAL_EASTER_DEFAULT);

        /* Year 1500: With ALWAYS_GREGORIAN mode, uses Gregorian calendar */
        $gregorianResult = $this->easter_days(1500, Easter::CAL_EASTER_ALWAYS_GREGORIAN);

        /* The results should be different */
        $julianResult->shouldNotEqual($gregorianResult->getWrappedObject());
    }

    /**
     * Test easter_days with CAL_EASTER_ROMAN mode.
     * For year 1700 (between 1583 and 1752):
     * - DEFAULT uses Julian (year <= 1752)
     * - ROMAN uses Gregorian (year > 1582)
     */
    public function it_calculates_easter_days_with_roman_mode(): void
    {
        /* Year 1700: With DEFAULT mode, uses Julian calendar */
        $defaultResult = $this->easter_days(1700, Easter::CAL_EASTER_DEFAULT);

        /* Year 1700: With ROMAN mode, uses Gregorian calendar */
        $romanResult = $this->easter_days(1700, Easter::CAL_EASTER_ROMAN);

        /* The results should be different */
        $defaultResult->shouldNotEqual($romanResult->getWrappedObject());
    }

    /**
     * Test easter_date with mode parameter.
     */
    public function it_calculates_easter_date_with_mode(): void
    {
        /* Year 2000: Gregorian Easter = April 23, 2000 (timestamp 956448000) */
        $this->easter_date(2000, Easter::CAL_EASTER_DEFAULT)->shouldReturn(956448000);

        /* Year 2000: Julian Easter = April 17, 2000 (timestamp 955929600) */
        $this->easter_date(2000, Easter::CAL_EASTER_ALWAYS_JULIAN)->shouldReturn(955929600);
    }
}
