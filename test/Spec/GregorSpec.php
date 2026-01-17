<?php

declare(strict_types=1);

namespace Spec\Roukmoute\Polyfill\Calendar;

use PhpSpec\ObjectBehavior;
use Roukmoute\Polyfill\Calendar\Gregor;

class GregorSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Gregor::class);
    }

    /**
     * Test cases from PHP source: ext/calendar/tests/gregoriantojd.phpt
     */
    public function it_calculates_julian_day_count_from_gregorian_date(): void
    {
        $this->gregoriantojd(0, 0, 0)->shouldReturn(0);
        $this->gregoriantojd(1, 1, 1582)->shouldReturn(2298874);
        $this->gregoriantojd(10, 5, 1582)->shouldReturn(2299151);
        $this->gregoriantojd(1, 1, 1970)->shouldReturn(2440588);
        $this->gregoriantojd(1, 1, 2999)->shouldReturn(2816423);
        $this->gregoriantojd(1, 1, -4714)->shouldReturn(0);
        $this->gregoriantojd(11, 24, -4714)->shouldReturn(0);
    }

    public function it_returns_zero_for_invalid_dates(): void
    {
        /* Year 0 does not exist */
        $this->gregoriantojd(1, 1, 0)->shouldReturn(0);

        /* Invalid month */
        $this->gregoriantojd(0, 1, 2000)->shouldReturn(0);
        $this->gregoriantojd(13, 1, 2000)->shouldReturn(0);

        /* Invalid day */
        $this->gregoriantojd(1, 0, 2000)->shouldReturn(0);
        $this->gregoriantojd(1, 32, 2000)->shouldReturn(0);

        /* Year before supported range */
        $this->gregoriantojd(1, 1, -4715)->shouldReturn(0);
    }

    public function it_handles_boundary_dates(): void
    {
        /* First valid SDN date: Nov 25, 4714 B.C. */
        $this->gregoriantojd(11, 25, -4714)->shouldReturn(1);

        /* Common dates */
        $this->gregoriantojd(12, 25, 2019)->shouldReturn(2458843);
        $this->gregoriantojd(7, 4, 1776)->shouldReturn(2369916);
    }

    /**
     * Test case from PHP source: ext/calendar/tests/gregoriantojd_overflow.phpt
     * 64-bit only test
     */
    public function it_handles_large_year_values_on_64bit_systems(): void
    {
        if (PHP_INT_SIZE !== 8) {
            return;
        }

        $this->gregoriantojd(5, 5, 6000000)->shouldReturn(2193176185);
    }
}
