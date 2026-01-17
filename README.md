# Calendar Polyfill

[![Tests](https://github.com/roukmoute/polyfill-calendar/workflows/Tests/badge.svg)](https://github.com/roukmoute/polyfill-calendar/actions)
[![License](https://img.shields.io/github/license/roukmoute/polyfill-calendar)](LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/roukmoute/polyfill-calendar)](composer.json)

A PHP polyfill for the [calendar extension](https://www.php.net/manual/en/book.calendar.php).

This library provides a pure PHP implementation of all calendar extension functions, allowing you to use calendar functionality without requiring the calendar extension to be installed.

## Requirements

- PHP 8.0 or higher

## Installation

```sh
composer require roukmoute/polyfill-calendar
```

## Supported Calendars

| Constant | Calendar |
|----------|----------|
| `CAL_GREGORIAN` | Gregorian Calendar |
| `CAL_JULIAN` | Julian Calendar |
| `CAL_JEWISH` | Jewish Calendar |
| `CAL_FRENCH` | French Republican Calendar |

## Available Functions

### Calendar Conversion

| Function | Description |
|----------|-------------|
| [`cal_to_jd`](https://www.php.net/manual/en/function.cal-to-jd.php) | Converts from a supported calendar to Julian Day Count |
| [`cal_from_jd`](https://www.php.net/manual/en/function.cal-from-jd.php) | Converts from Julian Day Count to a supported calendar |
| [`cal_days_in_month`](https://www.php.net/manual/en/function.cal-days-in-month.php) | Return the number of days in a month for a given year and calendar |
| [`cal_info`](https://www.php.net/manual/en/function.cal-info.php) | Returns information about a particular calendar |

### Gregorian Calendar

| Function | Description |
|----------|-------------|
| [`gregoriantojd`](https://www.php.net/manual/en/function.gregoriantojd.php) | Converts a Gregorian date to Julian Day Count |
| [`jdtogregorian`](https://www.php.net/manual/en/function.jdtogregorian.php) | Converts Julian Day Count to Gregorian date |

### Julian Calendar

| Function | Description |
|----------|-------------|
| [`juliantojd`](https://www.php.net/manual/en/function.juliantojd.php) | Converts a Julian Calendar date to Julian Day Count |
| [`jdtojulian`](https://www.php.net/manual/en/function.jdtojulian.php) | Converts a Julian Day Count to Julian Calendar Date |

### Jewish Calendar

| Function | Description |
|----------|-------------|
| [`jewishtojd`](https://www.php.net/manual/en/function.jewishtojd.php) | Converts a date in the Jewish Calendar to Julian Day Count |
| [`jdtojewish`](https://www.php.net/manual/en/function.jdtojewish.php) | Converts a Julian Day Count to the Jewish Calendar |

### French Republican Calendar

| Function | Description |
|----------|-------------|
| [`frenchtojd`](https://www.php.net/manual/en/function.frenchtojd.php) | Converts a date from the French Republican Calendar to a Julian Day Count |
| [`jdtofrench`](https://www.php.net/manual/en/function.jdtofrench.php) | Converts a Julian Day Count to French Republican Calendar Date |

### Easter Functions

| Function | Description |
|----------|-------------|
| [`easter_date`](https://www.php.net/manual/en/function.easter-date.php) | Get Unix timestamp for midnight on Easter of a given year |
| [`easter_days`](https://www.php.net/manual/en/function.easter-days.php) | Get number of days after March 21 on which Easter falls for a given year |

### Unix Timestamp Conversion

| Function | Description |
|----------|-------------|
| [`unixtojd`](https://www.php.net/manual/en/function.unixtojd.php) | Convert Unix timestamp to Julian Day |
| [`jdtounix`](https://www.php.net/manual/en/function.jdtounix.php) | Convert Julian Day to Unix timestamp |

### Utility Functions

| Function | Description |
|----------|-------------|
| [`jddayofweek`](https://www.php.net/manual/en/function.jddayofweek.php) | Returns the day of the week for a Julian Day |
| [`jdmonthname`](https://www.php.net/manual/en/function.jdmonthname.php) | Returns a month name |

## Usage Examples

### Converting between calendars

```php
// Gregorian to Julian Day Count
$jd = gregoriantojd(12, 25, 2024); // December 25, 2024

// Julian Day Count to Gregorian
$date = jdtogregorian($jd); // "12/25/2024"

// Convert to Jewish calendar
$jewish = jdtojewish($jd); // "10/23/5785"
```

### Getting Easter date

```php
// Get Easter Sunday timestamp for 2025
$easter = easter_date(2025);
echo date('Y-m-d', $easter); // 2025-04-20

// Get days after March 21
$days = easter_days(2025); // 30
```

### Calendar information

```php
// Get info about all calendars
$info = cal_info();

// Get info about a specific calendar
$gregorian = cal_info(CAL_GREGORIAN);
print_r($gregorian['months']); // Array of month names
```

### Days in a month

```php
// February 2024 (leap year)
$days = cal_days_in_month(CAL_GREGORIAN, 2, 2024); // 29

// February 2023 (not a leap year)
$days = cal_days_in_month(CAL_GREGORIAN, 2, 2023); // 28
```

## Available Constants

### Calendar Types
- `CAL_GREGORIAN`, `CAL_JULIAN`, `CAL_JEWISH`, `CAL_FRENCH`, `CAL_NUM_CALS`

### Day of Week Modes
- `CAL_DOW_DAYNO`, `CAL_DOW_SHORT`, `CAL_DOW_LONG`

### Month Name Modes
- `CAL_MONTH_GREGORIAN_SHORT`, `CAL_MONTH_GREGORIAN_LONG`
- `CAL_MONTH_JULIAN_SHORT`, `CAL_MONTH_JULIAN_LONG`
- `CAL_MONTH_JEWISH`, `CAL_MONTH_FRENCH`

### Easter Calculation Modes
- `CAL_EASTER_DEFAULT`, `CAL_EASTER_ROMAN`
- `CAL_EASTER_ALWAYS_GREGORIAN`, `CAL_EASTER_ALWAYS_JULIAN`

### Jewish Calendar Formatting
- `CAL_JEWISH_ADD_ALAFIM_GERESH`, `CAL_JEWISH_ADD_ALAFIM`, `CAL_JEWISH_ADD_GERESHAYIM`

## License

This library is released under the [MIT license](LICENSE).
