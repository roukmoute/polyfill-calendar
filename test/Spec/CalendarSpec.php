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

    /**
     * Test cases from PHP source: ext/calendar/tests/jdtounix.phpt
     */
    public function it_converts_julian_day_to_unix_timestamp(): void
    {
        /* JD 2440588 = 1970-01-01 (Unix epoch) */
        $this->jdtounix(2440588)->shouldReturn(0);

        /* JD 2452162 = 2001-09-09 */
        $this->jdtounix(2452162)->shouldReturn(999993600);

        /* JD 2453926 = 2006-07-09 */
        $this->jdtounix(2453926)->shouldReturn(1152403200);
    }

    /**
     * Test case from PHP source: ext/calendar/tests/jdtounix_error1.phpt
     */
    public function it_throws_an_exception_for_julian_day_before_unix_epoch(): void
    {
        $maxJd = \PHP_INT_SIZE === 8 ? 106751993607888 : 2465443;
        $this->shouldThrow(new ValueError('jdtounix(): jday must be between 2440588 and ' . $maxJd))
            ->duringJdtounix(2440579)
        ;
    }

    /**
     * Test cases from PHP source: ext/calendar/tests/gh16231.phpt
     */
    public function it_throws_an_exception_for_julian_day_integer_overflow(): void
    {
        $maxJd = \PHP_INT_SIZE === 8 ? 106751993607888 : 2465443;
        $this->shouldThrow(new ValueError('jdtounix(): jday must be between 2440588 and ' . $maxJd))
            ->duringJdtounix(\PHP_INT_MIN)
        ;
    }

    /**
     * Test cases from PHP source: ext/calendar/tests/bug80185.phpt (64-bit)
     * and ext/calendar/tests/bug80185_32bit.phpt (32-bit)
     */
    public function it_handles_julian_day_boundary_on_64bit_systems(): void
    {
        if (\PHP_INT_SIZE !== 8) {
            return;
        }

        /* JD 2465712 = 2038-08-16 */
        $this->jdtounix(2465712)->shouldReturn(2170713600);

        /* Maximum valid JD on 64-bit */
        $maxJd = (int) (\PHP_INT_MAX / 86400 + 2440588);
        $this->jdtounix($maxJd)->shouldReturn(9223372036854720000);

        /* One above maximum should throw */
        $this->shouldThrow(new ValueError('jdtounix(): jday must be between 2440588 and 106751993607888'))
            ->duringJdtounix($maxJd + 1)
        ;
    }

    public function it_handles_julian_day_boundary_on_32bit_systems(): void
    {
        if (\PHP_INT_SIZE !== 4) {
            return;
        }

        /* JD 2465712 exceeds 32-bit limit (max is 2465443) */
        $this->shouldThrow(new ValueError('jdtounix(): jday must be between 2440588 and 2465443'))
            ->duringJdtounix(2465712)
        ;

        /* Maximum valid JD on 32-bit */
        $maxJd = (int) (\PHP_INT_MAX / 86400 + 2440588);
        $this->jdtounix($maxJd)->shouldReturn(2147472000);

        /* One above maximum should throw */
        $this->shouldThrow(new ValueError('jdtounix(): jday must be between 2440588 and 2465443'))
            ->duringJdtounix($maxJd + 1)
        ;
    }

    /**
     * Test cases from PHP source: ext/calendar/tests/unixtojd.phpt
     */
    public function it_converts_unix_timestamp_to_julian_day(): void
    {
        /* unixtojd(40000) = 2440588 (still on day 1 of Unix epoch) */
        $this->unixtojd(40000)->shouldReturn(2440588);

        /* unixtojd(1000000000) = 2452162 (2001-09-09) */
        $this->unixtojd(1000000000)->shouldReturn(2452162);

        /* unixtojd(1152459009) = 2453926 (2006-07-09) */
        $this->unixtojd(1152459009)->shouldReturn(2453926);
    }

    public function it_converts_current_time_when_no_argument_given(): void
    {
        /* unixtojd() with no argument should return current JD */
        $expected = (int) (time() / 86400) + 2440588;
        $this->unixtojd()->shouldReturn($expected);

        /* unixtojd(null) should also return current JD */
        $this->unixtojd(null)->shouldReturn($expected);
    }

    /**
     * Test case from PHP source: ext/calendar/tests/unixtojd_error1.phpt
     */
    public function it_throws_an_exception_for_negative_timestamp(): void
    {
        $this->shouldThrow(new ValueError('unixtojd(): Argument #1 ($timestamp) must be greater than or equal to 0'))
            ->duringUnixtojd(-1)
        ;
    }

    /**
     * Test cases from PHP source: ext/calendar/tests/jddayofweek.phpt
     */
    public function it_returns_day_of_week_as_number(): void
    {
        /* JD 2440588 = Thursday (1970-01-01) */
        $this->jddayofweek(2440588, Calendar::CAL_DOW_DAYNO)->shouldReturn(4);

        /* JD 2452162 = Sunday (2001-09-09) */
        $this->jddayofweek(2452162, Calendar::CAL_DOW_DAYNO)->shouldReturn(0);

        /* JD 2453926 = Sunday (2006-07-09) */
        $this->jddayofweek(2453926, Calendar::CAL_DOW_DAYNO)->shouldReturn(0);

        /* Negative Julian Day: JD -1000 = Tuesday */
        $this->jddayofweek(-1000, Calendar::CAL_DOW_DAYNO)->shouldReturn(2);
    }

    public function it_returns_day_of_week_as_long_name(): void
    {
        $this->jddayofweek(2440588, Calendar::CAL_DOW_LONG)->shouldReturn('Thursday');
        $this->jddayofweek(2452162, Calendar::CAL_DOW_LONG)->shouldReturn('Sunday');
        $this->jddayofweek(-1000, Calendar::CAL_DOW_LONG)->shouldReturn('Tuesday');
    }

    public function it_returns_day_of_week_as_short_name(): void
    {
        $this->jddayofweek(2440588, Calendar::CAL_DOW_SHORT)->shouldReturn('Thu');
        $this->jddayofweek(2452162, Calendar::CAL_DOW_SHORT)->shouldReturn('Sun');
        $this->jddayofweek(-1000, Calendar::CAL_DOW_SHORT)->shouldReturn('Tue');
    }

    public function it_defaults_to_day_number_mode(): void
    {
        $this->jddayofweek(2440588)->shouldReturn(4);
    }

    /**
     * Test case from PHP source: ext/calendar/tests/gh16258.phpt
     * Overflow handling for extreme integer values
     */
    public function it_handles_integer_overflow_for_jddayofweek(): void
    {
        /* Should not crash or error with extreme values */
        $this->jddayofweek(\PHP_INT_MAX, Calendar::CAL_DOW_LONG)->shouldBeString();
        $this->jddayofweek(\PHP_INT_MIN, Calendar::CAL_DOW_LONG)->shouldBeString();
    }

    /**
     * Test cases from PHP source: ext/calendar/tests/jdmonthname.phpt
     */
    public function it_returns_gregorian_short_month_names(): void
    {
        /* JD 2440588 = 1970-01-01, mode 0 (CAL_MONTH_GREGORIAN_SHORT) */
        $this->jdmonthname(2440588, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Jan');
        $this->jdmonthname(2440588 + 30, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Jan');
        $this->jdmonthname(2440588 + 60, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Mar');
        $this->jdmonthname(2440588 + 90, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Apr');
        $this->jdmonthname(2440588 + 120, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('May');
        $this->jdmonthname(2440588 + 150, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('May');
        $this->jdmonthname(2440588 + 180, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Jun');
        $this->jdmonthname(2440588 + 210, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Jul');
        $this->jdmonthname(2440588 + 240, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Aug');
        $this->jdmonthname(2440588 + 270, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Sep');
        $this->jdmonthname(2440588 + 300, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Oct');
        $this->jdmonthname(2440588 + 330, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Nov');
        $this->jdmonthname(2440588 + 360, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Dec');
    }

    public function it_returns_gregorian_long_month_names(): void
    {
        /* JD 2440588 = 1970-01-01, mode 1 (CAL_MONTH_GREGORIAN_LONG) */
        $this->jdmonthname(2440588, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('January');
        $this->jdmonthname(2440588 + 30, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('January');
        $this->jdmonthname(2440588 + 60, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('March');
        $this->jdmonthname(2440588 + 90, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('April');
        $this->jdmonthname(2440588 + 120, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('May');
        $this->jdmonthname(2440588 + 150, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('May');
        $this->jdmonthname(2440588 + 180, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('June');
        $this->jdmonthname(2440588 + 210, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('July');
        $this->jdmonthname(2440588 + 240, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('August');
        $this->jdmonthname(2440588 + 270, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('September');
        $this->jdmonthname(2440588 + 300, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('October');
        $this->jdmonthname(2440588 + 330, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('November');
        $this->jdmonthname(2440588 + 360, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('December');
    }

    public function it_returns_julian_short_month_names(): void
    {
        /* JD 2440588 = 1970-01-01, mode 2 (CAL_MONTH_JULIAN_SHORT) */
        $this->jdmonthname(2440588, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Dec');
        $this->jdmonthname(2440588 + 30, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Jan');
        $this->jdmonthname(2440588 + 60, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Feb');
        $this->jdmonthname(2440588 + 90, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Mar');
        $this->jdmonthname(2440588 + 120, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Apr');
        $this->jdmonthname(2440588 + 150, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('May');
        $this->jdmonthname(2440588 + 180, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Jun');
        $this->jdmonthname(2440588 + 210, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Jul');
        $this->jdmonthname(2440588 + 240, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Aug');
        $this->jdmonthname(2440588 + 270, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Sep');
        $this->jdmonthname(2440588 + 300, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Oct');
        $this->jdmonthname(2440588 + 330, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Nov');
        $this->jdmonthname(2440588 + 360, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Dec');
    }

    public function it_returns_julian_long_month_names(): void
    {
        /* JD 2440588 = 1970-01-01, mode 3 (CAL_MONTH_JULIAN_LONG) */
        $this->jdmonthname(2440588, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('December');
        $this->jdmonthname(2440588 + 30, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('January');
        $this->jdmonthname(2440588 + 60, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('February');
        $this->jdmonthname(2440588 + 90, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('March');
        $this->jdmonthname(2440588 + 120, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('April');
        $this->jdmonthname(2440588 + 150, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('May');
        $this->jdmonthname(2440588 + 180, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('June');
        $this->jdmonthname(2440588 + 210, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('July');
        $this->jdmonthname(2440588 + 240, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('August');
        $this->jdmonthname(2440588 + 270, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('September');
        $this->jdmonthname(2440588 + 300, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('October');
        $this->jdmonthname(2440588 + 330, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('November');
        $this->jdmonthname(2440588 + 360, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('December');
    }

    public function it_returns_jewish_month_names(): void
    {
        /* JD 2440588 = 1970-01-01, mode 4 (CAL_MONTH_JEWISH) */
        $this->jdmonthname(2440588, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Tevet');
        $this->jdmonthname(2440588 + 30, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Shevat');
        $this->jdmonthname(2440588 + 60, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Adar I');
        $this->jdmonthname(2440588 + 90, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Adar II');
        $this->jdmonthname(2440588 + 120, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Nisan');
        $this->jdmonthname(2440588 + 150, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Iyyar');
        $this->jdmonthname(2440588 + 180, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Sivan');
        $this->jdmonthname(2440588 + 210, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Tammuz');
        $this->jdmonthname(2440588 + 240, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Av');
        $this->jdmonthname(2440588 + 270, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Elul');
        $this->jdmonthname(2440588 + 300, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Tishri');
        $this->jdmonthname(2440588 + 330, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Heshvan');
        $this->jdmonthname(2440588 + 360, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Kislev');
    }

    public function it_returns_french_month_names(): void
    {
        /* JD 2440588 is outside French calendar range (1792-1806), should return empty */
        $this->jdmonthname(2440588, Calendar::CAL_MONTH_FRENCH)->shouldReturn('');

        /* French calendar valid range: JD 2375840-2380952 */
        $this->jdmonthname(2375840, Calendar::CAL_MONTH_FRENCH)->shouldReturn('Vendemiaire');
        $this->jdmonthname(2375870, Calendar::CAL_MONTH_FRENCH)->shouldReturn('Brumaire');
        $this->jdmonthname(2375900, Calendar::CAL_MONTH_FRENCH)->shouldReturn('Frimaire');
    }

    public function it_returns_jewish_month_names_for_jd_2452162(): void
    {
        /* JD 2452162 = 2001-09-09, mode 4 (CAL_MONTH_JEWISH) */
        $this->jdmonthname(2452162, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Elul');
        $this->jdmonthname(2452162 + 30, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Tishri');
        $this->jdmonthname(2452162 + 60, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Heshvan');
        $this->jdmonthname(2452162 + 90, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Kislev');
        $this->jdmonthname(2452162 + 120, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Tevet');
        $this->jdmonthname(2452162 + 150, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Shevat');
        $this->jdmonthname(2452162 + 180, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Adar');
        $this->jdmonthname(2452162 + 210, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Nisan');
        $this->jdmonthname(2452162 + 240, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Iyyar');
        $this->jdmonthname(2452162 + 270, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Sivan');
        $this->jdmonthname(2452162 + 300, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Tammuz');
        $this->jdmonthname(2452162 + 330, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Av');
        $this->jdmonthname(2452162 + 360, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Elul');
    }

    public function it_defaults_to_gregorian_short_for_invalid_mode(): void
    {
        /* Mode 6 and any invalid mode should default to CAL_MONTH_GREGORIAN_SHORT */
        $this->jdmonthname(2440588, 6)->shouldReturn('Jan');
        $this->jdmonthname(2452162, 6)->shouldReturn('Sep');
    }

    /**
     * Test cases from PHP source: ext/calendar/tests/jdtomonthname.phpt
     */
    public function it_returns_month_names_for_various_julian_days(): void
    {
        /* JD 2453396 = 2005-01-01 */
        $this->jdmonthname(2453396, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Jan');
        $this->jdmonthname(2453396, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('January');
        $this->jdmonthname(2453396, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Jan');
        $this->jdmonthname(2453396, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('January');
        $this->jdmonthname(2453396, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Shevat');
        $this->jdmonthname(2453396, Calendar::CAL_MONTH_FRENCH)->shouldReturn('');

        /* JD 10000000 - far future date */
        $this->jdmonthname(10000000, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('Dec');
        $this->jdmonthname(10000000, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('December');
        $this->jdmonthname(10000000, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('Jul');
        $this->jdmonthname(10000000, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('July');
        $this->jdmonthname(10000000, Calendar::CAL_MONTH_JEWISH)->shouldReturn('Tishri');
        $this->jdmonthname(10000000, Calendar::CAL_MONTH_FRENCH)->shouldReturn('');
    }

    public function it_returns_empty_string_for_invalid_julian_day(): void
    {
        /* JD -1 is invalid, should return empty for all modes */
        $this->jdmonthname(-1, Calendar::CAL_MONTH_GREGORIAN_SHORT)->shouldReturn('');
        $this->jdmonthname(-1, Calendar::CAL_MONTH_GREGORIAN_LONG)->shouldReturn('');
        $this->jdmonthname(-1, Calendar::CAL_MONTH_JULIAN_SHORT)->shouldReturn('');
        $this->jdmonthname(-1, Calendar::CAL_MONTH_JULIAN_LONG)->shouldReturn('');
        $this->jdmonthname(-1, Calendar::CAL_MONTH_JEWISH)->shouldReturn('');
        $this->jdmonthname(-1, Calendar::CAL_MONTH_FRENCH)->shouldReturn('');
    }

    /**
     * Test case from PHP source: ext/calendar/tests/bug71894.phpt
     * JD 347997 is at the boundary of Jewish calendar (year 0)
     */
    public function it_returns_empty_string_for_jewish_year_zero(): void
    {
        $this->jdmonthname(347997, Calendar::CAL_MONTH_JEWISH)->shouldReturn('');
    }

    /**
     * Test cases from PHP source: ext/calendar/tests/cal_from_jd.phpt
     */
    public function it_converts_julian_day_to_gregorian_calendar(): void
    {
        $result = $this->cal_from_jd(1748326, Calendar::CAL_GREGORIAN);
        $result->shouldBeArray();
        $result->shouldHaveKeyWithValue('date', '8/26/74');
        $result->shouldHaveKeyWithValue('month', 8);
        $result->shouldHaveKeyWithValue('day', 26);
        $result->shouldHaveKeyWithValue('year', 74);
        $result->shouldHaveKeyWithValue('dow', 0);
        $result->shouldHaveKeyWithValue('abbrevdayname', 'Sun');
        $result->shouldHaveKeyWithValue('dayname', 'Sunday');
        $result->shouldHaveKeyWithValue('abbrevmonth', 'Aug');
        $result->shouldHaveKeyWithValue('monthname', 'August');
    }

    public function it_converts_julian_day_to_julian_calendar(): void
    {
        $result = $this->cal_from_jd(1748324, Calendar::CAL_JULIAN);
        $result->shouldBeArray();
        $result->shouldHaveKeyWithValue('date', '8/26/74');
        $result->shouldHaveKeyWithValue('month', 8);
        $result->shouldHaveKeyWithValue('day', 26);
        $result->shouldHaveKeyWithValue('year', 74);
        $result->shouldHaveKeyWithValue('dow', 5);
        $result->shouldHaveKeyWithValue('abbrevdayname', 'Fri');
        $result->shouldHaveKeyWithValue('dayname', 'Friday');
        $result->shouldHaveKeyWithValue('abbrevmonth', 'Aug');
        $result->shouldHaveKeyWithValue('monthname', 'August');
    }

    public function it_converts_julian_day_to_jewish_calendar(): void
    {
        $result = $this->cal_from_jd(374867, Calendar::CAL_JEWISH);
        $result->shouldBeArray();
        $result->shouldHaveKeyWithValue('date', '8/26/74');
        $result->shouldHaveKeyWithValue('month', 8);
        $result->shouldHaveKeyWithValue('day', 26);
        $result->shouldHaveKeyWithValue('year', 74);
        $result->shouldHaveKeyWithValue('dow', 4);
        $result->shouldHaveKeyWithValue('abbrevdayname', 'Thu');
        $result->shouldHaveKeyWithValue('dayname', 'Thursday');
        $result->shouldHaveKeyWithValue('abbrevmonth', 'Nisan');
        $result->shouldHaveKeyWithValue('monthname', 'Nisan');
    }

    public function it_converts_julian_day_zero_to_french_calendar(): void
    {
        $result = $this->cal_from_jd(0, Calendar::CAL_FRENCH);
        $result->shouldBeArray();
        $result->shouldHaveKeyWithValue('date', '0/0/0');
        $result->shouldHaveKeyWithValue('month', 0);
        $result->shouldHaveKeyWithValue('day', 0);
        $result->shouldHaveKeyWithValue('year', 0);
        $result->shouldHaveKeyWithValue('dow', 1);
        $result->shouldHaveKeyWithValue('abbrevdayname', 'Mon');
        $result->shouldHaveKeyWithValue('dayname', 'Monday');
        $result->shouldHaveKeyWithValue('abbrevmonth', '');
        $result->shouldHaveKeyWithValue('monthname', '');
    }

    /**
     * Test case from PHP source: ext/calendar/tests/cal_from_jd_error1.phpt
     */
    public function it_throws_exception_for_invalid_calendar_in_cal_from_jd(): void
    {
        $this->shouldThrow(new ValueError('cal_from_jd(): Argument #2 ($calendar) must be a valid calendar ID'))
            ->duringCal_from_jd(1748326, -1)
        ;
    }

    /**
     * Test case from PHP source: ext/calendar/tests/bug71894.phpt
     * Jewish calendar with year 0 returns null dow
     */
    public function it_returns_null_dow_for_jewish_calendar_at_year_zero(): void
    {
        $result = $this->cal_from_jd(347997, Calendar::CAL_JEWISH);
        $result->shouldBeArray();
        $result->shouldHaveKeyWithValue('date', '0/0/0');
        $result->shouldHaveKeyWithValue('month', 0);
        $result->shouldHaveKeyWithValue('day', 0);
        $result->shouldHaveKeyWithValue('year', 0);
        $result->shouldHaveKeyWithValue('dow', null);
        $result->shouldHaveKeyWithValue('abbrevdayname', '');
        $result->shouldHaveKeyWithValue('dayname', '');
        $result->shouldHaveKeyWithValue('abbrevmonth', '');
        $result->shouldHaveKeyWithValue('monthname', '');
    }

    /**
     * Test cases from PHP source: ext/calendar/tests/bug53574_2.phpt (64-bit)
     * Integer overflow in SdnToJulian
     */
    public function it_handles_julian_overflow_on_64bit(): void
    {
        if (\PHP_INT_SIZE !== 8) {
            return;
        }

        $result = $this->cal_from_jd(3315881921229094912, Calendar::CAL_JULIAN);
        $result->shouldHaveKeyWithValue('date', '0/0/0');
        $result->shouldHaveKeyWithValue('month', 0);
        $result->shouldHaveKeyWithValue('day', 0);
        $result->shouldHaveKeyWithValue('year', 0);
        $result->shouldHaveKeyWithValue('dow', 3);
        $result->shouldHaveKeyWithValue('abbrevdayname', 'Wed');
        $result->shouldHaveKeyWithValue('dayname', 'Wednesday');
    }

    /**
     * Test cases from PHP source: ext/calendar/tests/bug55797_2.phpt (64-bit)
     * Integer overflow in SdnToGregorian
     */
    public function it_handles_gregorian_overflow_on_64bit(): void
    {
        if (\PHP_INT_SIZE !== 8) {
            return;
        }

        $result = $this->cal_from_jd(9223372036854743639, Calendar::CAL_GREGORIAN);
        $result->shouldHaveKeyWithValue('date', '0/0/0');
        $result->shouldHaveKeyWithValue('month', 0);
        $result->shouldHaveKeyWithValue('day', 0);
        $result->shouldHaveKeyWithValue('year', 0);
    }

    /**
     * Test cases from PHP source: ext/calendar/tests/cal_info.phpt
     */
    public function it_returns_info_for_all_calendars(): void
    {
        $result = $this->cal_info();
        $result->shouldBeArray();
        $result->shouldHaveCount(4);

        /* Gregorian calendar (index 0) */
        $result[0]->shouldHaveKeyWithValue('calname', 'Gregorian');
        $result[0]->shouldHaveKeyWithValue('calsymbol', 'CAL_GREGORIAN');
        $result[0]->shouldHaveKeyWithValue('maxdaysinmonth', 31);
        $result[0]['months']->shouldHaveKeyWithValue(1, 'January');
        $result[0]['months']->shouldHaveKeyWithValue(12, 'December');
        $result[0]['abbrevmonths']->shouldHaveKeyWithValue(1, 'Jan');
        $result[0]['abbrevmonths']->shouldHaveKeyWithValue(12, 'Dec');

        /* Julian calendar (index 1) */
        $result[1]->shouldHaveKeyWithValue('calname', 'Julian');
        $result[1]->shouldHaveKeyWithValue('calsymbol', 'CAL_JULIAN');
        $result[1]->shouldHaveKeyWithValue('maxdaysinmonth', 31);
        $result[1]['months']->shouldHaveKeyWithValue(1, 'January');
        $result[1]['months']->shouldHaveKeyWithValue(12, 'December');
        $result[1]['abbrevmonths']->shouldHaveKeyWithValue(1, 'Jan');
        $result[1]['abbrevmonths']->shouldHaveKeyWithValue(12, 'Dec');

        /* Jewish calendar (index 2) */
        $result[2]->shouldHaveKeyWithValue('calname', 'Jewish');
        $result[2]->shouldHaveKeyWithValue('calsymbol', 'CAL_JEWISH');
        $result[2]->shouldHaveKeyWithValue('maxdaysinmonth', 30);
        $result[2]['months']->shouldHaveKeyWithValue(1, 'Tishri');
        $result[2]['months']->shouldHaveKeyWithValue(6, 'Adar I');
        $result[2]['months']->shouldHaveKeyWithValue(7, 'Adar II');
        $result[2]['months']->shouldHaveKeyWithValue(13, 'Elul');
        $result[2]['abbrevmonths']->shouldHaveKeyWithValue(1, 'Tishri');
        $result[2]['abbrevmonths']->shouldHaveKeyWithValue(13, 'Elul');

        /* French calendar (index 3) */
        $result[3]->shouldHaveKeyWithValue('calname', 'French');
        $result[3]->shouldHaveKeyWithValue('calsymbol', 'CAL_FRENCH');
        $result[3]->shouldHaveKeyWithValue('maxdaysinmonth', 30);
        $result[3]['months']->shouldHaveKeyWithValue(1, 'Vendemiaire');
        $result[3]['months']->shouldHaveKeyWithValue(13, 'Extra');
        $result[3]['abbrevmonths']->shouldHaveKeyWithValue(1, 'Vendemiaire');
        $result[3]['abbrevmonths']->shouldHaveKeyWithValue(13, 'Extra');
    }

    public function it_returns_info_for_specific_calendar(): void
    {
        $result = $this->cal_info(Calendar::CAL_JULIAN);
        $result->shouldBeArray();
        $result->shouldHaveKeyWithValue('calname', 'Julian');
        $result->shouldHaveKeyWithValue('calsymbol', 'CAL_JULIAN');
        $result->shouldHaveKeyWithValue('maxdaysinmonth', 31);
        $result['months']->shouldHaveKeyWithValue(1, 'January');
        $result['months']->shouldHaveKeyWithValue(12, 'December');
        $result['abbrevmonths']->shouldHaveKeyWithValue(1, 'Jan');
        $result['abbrevmonths']->shouldHaveKeyWithValue(12, 'Dec');
    }

    public function it_throws_exception_for_invalid_calendar_in_cal_info(): void
    {
        $this->shouldThrow(new ValueError('cal_info(): Argument #1 ($calendar) must be a valid calendar ID'))
            ->duringCal_info(99999)
        ;
    }
}
