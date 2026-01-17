<?php

declare(strict_types=1);

namespace Spec\Roukmoute\Polyfill\Calendar;

use PhpSpec\ObjectBehavior;
use Roukmoute\Polyfill\Calendar\French;

class FrenchSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(French::class);
    }

    /**
     * Test cases from PHP source: ext/calendar/tests/frenchtojd.phpt
     */
    public function it_calculates_julian_day_count_from_french_republican_date(): void
    {
        $this->frenchtojd(-1, -1, -1)->shouldReturn(0);
        $this->frenchtojd(0, 0, 0)->shouldReturn(0);
        $this->frenchtojd(1, 1, 1)->shouldReturn(2375840);
        $this->frenchtojd(14, 31, 15)->shouldReturn(0);
    }

    public function it_returns_zero_for_invalid_dates(): void
    {
        /* Year must be between 1 and 14 */
        $this->frenchtojd(1, 1, 0)->shouldReturn(0);
        $this->frenchtojd(1, 1, 15)->shouldReturn(0);

        /* Month must be between 1 and 13 */
        $this->frenchtojd(0, 1, 1)->shouldReturn(0);
        $this->frenchtojd(14, 1, 1)->shouldReturn(0);

        /* Day must be between 1 and 30 */
        $this->frenchtojd(1, 0, 1)->shouldReturn(0);
        $this->frenchtojd(1, 31, 1)->shouldReturn(0);
    }

    public function it_handles_month_13_sansculottides(): void
    {
        /* Month 13 (Sansculottides) has 5 days in regular years */
        $this->frenchtojd(13, 5, 1)->shouldReturn(2376204);
        $this->frenchtojd(13, 6, 1)->shouldReturn(0); /* 6th day invalid in non-leap year */

        /* Leap years (3, 7, 11) have 6 days in month 13 */
        $this->frenchtojd(13, 6, 3)->shouldReturn(2376936);
        $this->frenchtojd(13, 6, 7)->shouldReturn(2378398);
        $this->frenchtojd(13, 6, 11)->shouldReturn(2379860);
    }

    public function it_handles_boundary_dates(): void
    {
        /* Last valid date: year 14, month 13, day 5 */
        $this->frenchtojd(13, 5, 14)->shouldReturn(2380952);

        /* Common dates */
        $this->frenchtojd(1, 1, 14)->shouldReturn(2380588);
    }
}
