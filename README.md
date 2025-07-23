# PHP SDK for Web Distribution

[![Tests](https://github.com/pdfsystems/web-distribution-sdk/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/pdfsystems/web-distribution-sdk/actions/workflows/run-tests.yml)

PHP SDK for interfacing With PDF Systems' Web Distribution ERP system for home furnishing companies.

## Installation

You can install the package via composer:

```bash
composer require pdfsystems/web-distribution-sdk
```

## Usage

```php
$skeleton = new Pdfsystems\WebDistributionSdk();
echo $skeleton->echoPhrase('Hello, Pdfsystems!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Rob Pungello](https://github.com/rpungello)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
