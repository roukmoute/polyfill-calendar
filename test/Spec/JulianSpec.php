<?php

declare(strict_types=1);

namespace Spec\Roukmoute\Polyfill\Calendar;

use PhpSpec\ObjectBehavior;
use Roukmoute\Polyfill\Calendar\Julian;

class JulianSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Julian::class);
    }

    public function it_calculates_gregorian_date_from_julian_day_count()
    {
        $this->jdtogregorian(2458489)->shouldReturn('1/5/2019');
        $this->jdtogregorian(2458465)->shouldReturn('12/12/2018');
        $this->jdtogregorian(0)->shouldReturn('0/0/0');
        $this->jdtogregorian(1)->shouldReturn('11/25/-4714');
        $this->jdtogregorian(2816423)->shouldReturn('1/1/2999');
        $this->jdtogregorian(536838866)->shouldReturn('10/17/1465102');
        $this->jdtogregorian(536838867)->shouldReturn('0/0/0');
    }

    public function it_calculates_julian_day_count_from_jewish_date()
    {
        $this->jewishtojd(4, 4, 5779)->shouldReturn(2458465);
    }

    public function it_calculates_julian_day_count_from_julian_calendar_date()
    {
        $this->juliantojd(12, 25, 2019)->shouldReturn(2458856);
    }
}
