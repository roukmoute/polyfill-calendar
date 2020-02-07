# Calendar Polyfill ![CI](https://github.com/roukmoute/polyfill-calendar/workflows/CI/badge.svg)

This project backports features found in the calendar extension.  
It is intended to be used when calendar extension is not enabled.

Currently, functions available are:
- [`easter_date`](https://www.php.net/manual/en/function.easter-date.php) — Get Unix timestamp for midnight on Easter of a given year
- [`easter_days`](https://www.php.net/manual/en/function.easter-days.php) — Get number of days after March 21 on which Easter falls for a given year

## Usage

### Installation

```sh
composer require roukmoute/polyfill-calendar
```

## License

This library is released under the [MIT license](LICENSE).
