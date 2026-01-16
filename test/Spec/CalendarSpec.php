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

    /* cal_days_in_month.phpt - Basic Gregorian tests */
    public function it_calculates_days_in_month_for_gregorian_calendar(): void
    {
        $this->cal_days_in_month(Calendar::CAL_GREGORIAN, 8, 2003)->shouldReturn(31);
        $this->cal_days_in_month(Calendar::CAL_GREGORIAN, 2, 2003)->shouldReturn(28);
        $this->cal_days_in_month(Calendar::CAL_GREGORIAN, 2, 2004)->shouldReturn(29); /* leap year */
        $this->cal_days_in_month(Calendar::CAL_GREGORIAN, 12, 2034)->shouldReturn(31);
    }

    /* cal_days_in_month_error1.phpt - Error handling */
    public function it_throws_an_exception_for_invalid_calendar_id(): void
    {
        $this->shouldThrow(new ValueError('cal_days_in_month(): Argument #1 ($calendar) must be a valid calendar ID'))
            ->duringCal_days_in_month(-1, 4, 2017)
        ;
    }

    public function it_returns_zero_for_invalid_month(): void
    {
        /* Month 20 is invalid, toSDN returns 0, so cal_days_in_month returns 0 */
        $this->cal_days_in_month(Calendar::CAL_GREGORIAN, 20, 2009)->shouldReturn(0);
    }

    /* bug52744.phpt - December 1 BCE */
    public function it_calculates_days_in_december_1_bce(): void
    {
        $this->cal_days_in_month(Calendar::CAL_GREGORIAN, 12, -1)->shouldReturn(31);
        $this->cal_days_in_month(Calendar::CAL_JULIAN, 12, -1)->shouldReturn(31);
    }

    /* bug67976.phpt - French calendar month 13 */
    public function it_calculates_days_in_french_calendar_month_13(): void
    {
        $this->cal_days_in_month(Calendar::CAL_FRENCH, 13, 14)->shouldReturn(5);
    }

    /* bug54254.phpt - Jewish calendar months */
    public function it_calculates_days_in_jewish_calendar_months(): void
    {
        /* Year 5771 - non-leap year */
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 1, 5771)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 2, 5771)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 3, 5771)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 4, 5771)->shouldReturn(29);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 5, 5771)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 6, 5771)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 7, 5771)->shouldReturn(29);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 8, 5771)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 9, 5771)->shouldReturn(29);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 10, 5771)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 11, 5771)->shouldReturn(29);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 12, 5771)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 13, 5771)->shouldReturn(29);

        /* Year 5772 - leap year (month 6 has days) */
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 1, 5772)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 2, 5772)->shouldReturn(29);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 3, 5772)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 4, 5772)->shouldReturn(29);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 5, 5772)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 6, 5772)->shouldReturn(0);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 7, 5772)->shouldReturn(29);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 8, 5772)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 9, 5772)->shouldReturn(29);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 10, 5772)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 11, 5772)->shouldReturn(29);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 12, 5772)->shouldReturn(30);
        $this->cal_days_in_month(Calendar::CAL_JEWISH, 13, 5772)->shouldReturn(29);
    }

    /* gh19371.phpt - Integer overflow handling */
    public function it_throws_an_exception_for_year_overflow(): void
    {
        $this->shouldThrow(new ValueError('cal_days_in_month(): Argument #3 ($year) must be less than 2147483646'))
            ->duringCal_days_in_month(Calendar::CAL_GREGORIAN, 12, \PHP_INT_MAX)
        ;
    }

    public function it_throws_an_exception_for_month_underflow(): void
    {
        $this->shouldThrow(new ValueError('cal_days_in_month(): Argument #2 ($month) must be between 1 and 2147483646'))
            ->duringCal_days_in_month(Calendar::CAL_GREGORIAN, \PHP_INT_MIN, 1)
        ;
    }

    public function it_throws_an_exception_for_month_overflow(): void
    {
        $this->shouldThrow(new ValueError('cal_days_in_month(): Argument #2 ($month) must be between 1 and 2147483646'))
            ->duringCal_days_in_month(Calendar::CAL_GREGORIAN, \PHP_INT_MAX, 1)
        ;
    }
}
