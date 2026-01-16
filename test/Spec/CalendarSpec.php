<?php

declare(strict_types=1);

namespace Spec\Roukmoute\Polyfill\Calendar;

use PhpSpec\ObjectBehavior;
use Roukmoute\Polyfill\Calendar\Calendar;
use Roukmoute\Polyfill\Calendar\ValueError;

class CalendarSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Calendar::class);
    }

    public function it_calculates_cal_to_jd(): void
    {
        $this->cal_to_jd(Calendar::CAL_GREGORIAN, 8, 26, 74)->shouldReturn(1748326);
        $this->cal_to_jd(Calendar::CAL_JULIAN, 8, 26, 74)->shouldReturn(1748324);
        $this->cal_to_jd(Calendar::CAL_JEWISH, 8, 26, 74)->shouldReturn(374867);
        $this->cal_to_jd(Calendar::CAL_FRENCH, 8, 26, 74)->shouldReturn(0);
    }

    public function it_throws_an_exception_cal_to_jd_when_first_argument_calendar_is_negative(): void
    {
        $this->shouldThrow(new ValueError('Argument #1 ($calendar) must be a valid calendar ID'))->duringCal_to_jd(-1, 8, 26, 74);
    }
}
