<?php

declare(strict_types=1);

namespace Spec\Roukmoute\Polyfill\Calendar;

use PhpSpec\ObjectBehavior;
use Roukmoute\Polyfill\Calendar\Easter;
use Roukmoute\Polyfill\Calendar\ValueError;

class EasterSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Easter::class);
    }

    public function it_calculates_easter_date_for_year_2000()
    {
        $this->easter_date(2000)->shouldReturn(956448000);
    }

    public function it_calculates_easter_date_for_year_2001()
    {
        $this->easter_date(2001)->shouldReturn(987292800);
    }

    public function it_calculates_easter_date_for_year_2002()
    {
        $this->easter_date(2002)->shouldReturn(1017532800);
    }

    public function it_throws_an_exception_when_year_is_before_1970()
    {
        $this->shouldThrow(ValueError::class)->during('easter_date', [1969]);
    }

    public function it_throws_an_exception_when_year_is_after_2037()
    {
        $this->shouldThrow(ValueError::class)->during('easter_date', [2038]);
    }

    public function it_calculates_easter_days_for_year_1999()
    {
        $this->easter_days(1999)->shouldReturn(14);
    }

    public function it_calculates_easter_days_for_year_1492()
    {
        $this->easter_days(1492)->shouldReturn(32);
    }

    public function it_calculates_easter_days_for_year_1913()
    {
        $this->easter_days(1913)->shouldReturn(2);
    }
}
