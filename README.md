# Soap WSDL

This package provides an easy way to handle WSDL data with **zero dependencies**.

## Install

You can install this package via composer. 

``` bash
composer require vaclavvanik/soap-wsdl
```

## Usage

### StringProvider

Provides WSDL from string variable.

```php
<?php

declare(strict_types=1);

use VaclavVanik\Soap\Wsdl;

$wsdl = (new Wsdl\StringProvider('wsdl-in-string-variable'))->provide();
```

### FileProvider

Provides WSDL from file.

```php
<?php

declare(strict_types=1);

use VaclavVanik\Soap\Wsdl;

$wsdl = (new Wsdl\FileProvider('my-file.wsdl'))->provide();
```

### CacheFileProvider

Loads and save $wsdl from delegated WsdlProvider to cache file.

```php
<?php

declare(strict_types=1);

use VaclavVanik\Soap\Wsdl;

$fileProvider = new Wsdl\FileProvider('my-file.wsdl');
$directory = '/tmp';
$ttl = 3600;

$wsdl = (new Wsdl\CacheFileProvider($fileProvider, $directory, $ttl))->provide();
```

### ChainProvider

Provides WSDL from first available WsdlProvider.

```php
<?php

declare(strict_types=1);

use VaclavVanik\Soap\Wsdl;

$fileProvider1 = new Wsdl\FileProvider('/may-be-unreachable/my-file.wsdl');
$fileProvider2 = new Wsdl\FileProvider('/should-be-reachable/my-file.wsdl');

$wsdl = (new Wsdl\ChainProvider($fileProvider1, $fileProvider2))->provide();
```

## Exceptions

provide methods throw:

- [Exception\EmptyContent](src/Exception/EmptyContent.php) if provided WSDL is empty.
- [Exception\File](src/Exception/File.php) if error occurs when accessing file.
- [Exception\Runtime](src/Exception/Runtime.php) if any other error occurs.

## Run check - coding standards and php-unit

Install dependencies:

```bash
make install
```

Run check:

```bash
make check
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
