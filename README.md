# Holidays

## Deprecated

This library is deprecated in favor of https://github.com/mll-lab/php-utils.

[![Continuous Integration](https://github.com/mll-lab/holidays/workflows/Continuous%20Integration/badge.svg)](https://github.com/mll-lab/holidays/actions)
[![Code Coverage](https://codecov.io/gh/mll-lab/holidays/branch/master/graph/badge.svg)](https://codecov.io/gh/mll-lab/holidays)
[![StyleCI](https://github.styleci.io/repos/414166520/shield?branch=master)](https://github.styleci.io/repos/414166520)

[![Latest Stable Version](https://poser.pugx.org/mll-lab/holidays/v/stable)](https://packagist.org/packages/mll-lab/holidays)
[![Total Downloads](https://poser.pugx.org/mll-lab/holidays/downloads)](https://packagist.org/packages/mll-lab/holidays)

## Installation

Install through composer

```sh
composer require mll-lab/holidays
```

## Usage

```php
use MLL\Holidays\BavarianHolidays;

// Call static methods on BavarianHolidays
```

### Custom Holidays

You can add custom holidays by registering a method that returns a map of holidays for a given year.
Set this up in a central place that always runs before your application, e.g. a bootstrap method.

```php
use MLL\Holidays\BavarianHolidays;

BavarianHolidays::$loadUserDefinedHolidays = static function (int $year): array {
    switch ($year) {
        case 2019:
            return ['22.03' => 'Day of the Tentacle'];
        default:
            return [];
    }
};
```

Custom holidays have precedence over the holidays inherent to this library.

## Changelog

See [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

See [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## License

This package is licensed using the MIT License.
