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
    }
}
