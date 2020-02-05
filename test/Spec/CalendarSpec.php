<?php

declare(strict_types=1);

namespace Spec\Roukmoute\Polyfill\Calendar;

use PhpSpec\ObjectBehavior;
use Roukmoute\Polyfill\Calendar\Calendar;
use Roukmoute\Polyfill\Calendar\ValueError;

class CalendarSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Calendar::class);
    }

    public function it_calculates_easter_for_year_2000()
    {
        $this->easter_date(2000)->shouldReturn(956448000);
    }

    public function it_calculates_easter_for_year_2001()
    {
        $this->easter_date(2001)->shouldReturn(987292800);
    }

    public function it_calculates_easter_for_year_2002()
    {
        $this->easter_date(2002)->shouldReturn(1017532800);
    }

    public function it_throws_an_exception_when_year_is_before_1970()
    {
        $this->shouldThrow(ValueError::class)->during('easter_date', [1969]);
    }

    public function it_throws_an_exception_when_year_is_after_()
    {
        $this->shouldThrow(ValueError::class)->during('easter_date', [2038]);
    }
}
