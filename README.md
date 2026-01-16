# Calendar Polyfill ![CI](https://github.com/roukmoute/polyfill-calendar/workflows/CI/badge.svg)

This project backports features found in the calendar extension.  
It is intended to be used when calendar extension is not enabled.

Currently, functions available are:
- [`cal_days_in_month`](https://www.php.net/manual/en/function.cal-days-in-month.php) — Return the number of days in a month for a given year and calendar
- [`cal_to_jd`](https://www.php.net/manual/en/function.cal-to-jd.php) — Converts from a supported calendar to Julian Day Count
- [`easter_date`](https://www.php.net/manual/en/function.easter-date.php) — Get Unix timestamp for midnight on Easter of a given year
- [`easter_days`](https://www.php.net/manual/en/function.easter-days.php) — Get number of days after March 21 on which Easter falls for a given year
- [`jdtogregorian`](https://www.php.net/manual/en/function.jdtogregorian.php) — Converts Julian Day Count to Gregorian date
- [`jdtojewish`](https://www.php.net/manual/en/function.jdtojewish.php) — Converts a Julian Day Count to the Jewish Calendar
- [`jewishtojd`](https://www.php.net/manual/en/function.jewishtojd.php) — Converts a date in the Jewish Calendar to Julian Day Count
- [`juliantojd`](https://www.php.net/manual/en/function.juliantojd.php) — Converts a Julian Calendar date to Julian Day Count

## Usage

### Installation

```sh
composer require roukmoute/polyfill-calendar
```

## License

This library is released under the [MIT license](LICENSE).
