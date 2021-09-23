# Flysystem adapter for Microsoft Sharepoint

[![Latest Version on Packagist](https://img.shields.io/packagist/v/homedesignshops/flysystem-sharepoint.svg?style=flat-square)](https://packagist.org/packages/homedesignshops/flysystem-sharepoint)
[![Total Downloads](https://img.shields.io/packagist/dt/homedesignshops/flysystem-sharepoint.svg?style=flat-square)](https://packagist.org/packages/homedesignshops/flysystem-sharepoint)
![GitHub Actions](https://github.com/homedesignshops/flysystem-sharepoint/actions/workflows/main.yml/badge.svg)

This package contains a Flysystem adapter for Sharepoint. Under the hood, the Microsoft Graph API v1 is used.
You need an Azure App to use this package.

## Installation

You can install the package via composer:

```bash
composer require homedesignshops/flysystem-sharepoint
```

## Usage

The first thing you need to do is [creating a new Azure Application](https://portal.azure.com/#blade/Microsoft_AAD_IAM/ActiveDirectoryMenuBlade/RegisteredApps).
Make sure you set the Microsoft Graph API Permissions inside your created application:
- Files.ReadWrite.All
- Group.ReadWrite.All

After that, you can use the adapter as follows:

```php
use Homedesignshops\FlysystemSharepoint\SharepointClient;
use Homedesignshops\FlysystemSharepoint\SharepointAdapter;
use League\Flysystem\Filesystem;

$client = new SharepointClient($tenantId, $clientId, $clientSecret, $sharepointGroupName)
$adapter = new SharepointAdapter($client, $pathPrefix);

$filesystem = new Filesystem($adapter);
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email kevin@homedesignshops.nl instead of using the issue tracker.

## Credits

-   [Kevin Koenen](https://github.com/homedesignshops)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.