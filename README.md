# Laravel & Lumen installer

[![Latest Version on Packagist](https://img.shields.io/packagist/v/DenarX/laravel-installer.svg)](https://packagist.org/packages/DenarX/laravel-installer)
[![Total Downloads](https://img.shields.io/packagist/dt/DenarX/laravel-installer.svg)](https://packagist.org/packages/DenarX/laravel-installer)
[![License](https://img.shields.io/github/license/DenarX/laravel-installer.svg)](LICENSE.md)

<!-- TOC -->

-   [Installation](#installation)
-   [License](#license)

<!-- /TOC -->

## Installation

You can install the package with [Composer](https://getcomposer.org/) using the following command:

```bash
composer require DenarX/laravel-installer
```

### For Lumen need to register service in bootstrap/app.php

```php
$app->register(\Denarx\laravelInstaller\InstallServiceProvider::class);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
