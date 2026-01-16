<?php

declare(strict_types=1);

namespace Spec\Roukmoute\Polyfill\Calendar;

use PhpSpec\ObjectBehavior;
use Roukmoute\Polyfill\Calendar\Jewish;

class JewishSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Jewish::class);
    }

    public function it_calculates_julian_day_count_from_jewish_date(): void
    {
        $this->jewishtojd(-1, -1, -1)->shouldReturn(0);
        $this->jewishtojd(0, 0, 0)->shouldReturn(0);
        $this->jewishtojd(1, 1, 1)->shouldReturn(347998);
        $this->jewishtojd(4, 4, 5779)->shouldReturn(2458465);
        $this->jewishtojd(4, 4, 5780)->shouldReturn(2458850);
    }

    public function it_converts_julian_day_to_jewish_date(): void
    {
        /* Invalid dates return 0/0/0 */
        $this->jdtojewish(0)->shouldReturn('0/0/0');
        $this->jdtojewish(347997)->shouldReturn('0/0/0');

        /* Valid dates from PHP jdtojewish.phpt */
        $this->jdtojewish(347998)->shouldReturn('1/1/1');
        $this->jdtojewish(2452576)->shouldReturn('2/22/5763'); /* Oct 28, 2002 */
        $this->jdtojewish(2452556)->shouldReturn('2/2/5763');  /* Oct 8, 2002 */
        $this->jdtojewish(2452584)->shouldReturn('2/30/5763'); /* Nov 5, 2002 */
        $this->jdtojewish(2454170)->shouldReturn('7/20/5767'); /* Mar 10, 2007 */
        $this->jdtojewish(2453339)->shouldReturn('3/16/5765'); /* Nov 29, 2004 */
        $this->jdtojewish(5372755)->shouldReturn('3/8/13758'); /* Jan 1, 9998 */
        $this->jdtojewish(2458465)->shouldReturn('4/4/5779');
        $this->jdtojewish(2458850)->shouldReturn('4/4/5780');
    }

    public function it_handles_overflow_correctly(): void
    {
        /* From PHP jdtojewish_overflow.phpt - max valid JD is 324542846 */
        $this->jdtojewish(324542846)->shouldReturn('12/13/887605');
        $this->jdtojewish(324542847)->shouldReturn('0/0/0');
        $this->jdtojewish(324542850)->shouldReturn('0/0/0');
    }

    public function it_converts_julian_day_to_jewish_date_in_hebrew(): void
    {
        /* Hebrew format for Tishri 1, year 5784 (Rosh Hashanah 2023) */
        /* JD 2460204 = 1 Tishri 5784 */
        $this->jdtojewish(2460204, true)->shouldReturn("\xE0 \xFA\xF9\xF8\xE9 \xE4\xFA\xF9\xF4\xE3");
    }

    public function it_converts_julian_day_to_jewish_date_in_hebrew_with_gereshayim(): void
    {
        /* With gereshayim flag */
        $this->jdtojewish(2460204, true, Jewish::CAL_JEWISH_ADD_GERESHAYIM)
            ->shouldReturn("\xE0' \xFA\xF9\xF8\xE9 \xE4\xFA\xF9\xF4\"\xE3");
    }

    public function it_converts_julian_day_to_jewish_date_in_hebrew_with_alafim_geresh(): void
    {
        /* With alafim geresh flag */
        $this->jdtojewish(2460204, true, Jewish::CAL_JEWISH_ADD_ALAFIM_GERESH)
            ->shouldReturn("\xE0 \xFA\xF9\xF8\xE9 \xE4'\xFA\xF9\xF4\xE3");
    }

    public function it_converts_julian_day_to_jewish_date_in_hebrew_with_combined_flags(): void
    {
        /* Combined flags */
        $flags = Jewish::CAL_JEWISH_ADD_ALAFIM_GERESH | Jewish::CAL_JEWISH_ADD_GERESHAYIM;
        $this->jdtojewish(2460204, true, $flags)
            ->shouldReturn("\xE0' \xFA\xF9\xF8\xE9 \xE4'\xFA\xF9\xF4\"\xE3");
    }
}
