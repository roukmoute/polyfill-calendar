<?php

declare(strict_types=1);

namespace Spec\Roukmoute\Polyfill\Calendar;

use PhpSpec\ObjectBehavior;
use Roukmoute\Polyfill\Calendar\Jewish;

class JewishSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Jewish::class);
    }

    public function it_calculates_julian_day_count_from_jewish_date()
    {
        $this->jewishtojd(-1, -1, -1)->shouldReturn(0);
        $this->jewishtojd(0, 0, 0)->shouldReturn(0);
        $this->jewishtojd(1, 1, 1)->shouldReturn(347998);
        $this->jewishtojd(4, 4, 5779)->shouldReturn(2458465);
    }
}
