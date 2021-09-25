# CPR Validator

Validates and scans text for valid Danish CPR numbers.

## Usage

```sh
composer require itk-dev/cpr-validator
```

```php
<?php

require_once 'vendor/autoload.php';

use ItkDev\CprValidator\CprValidator;

$cprValidator = new CprValidator();
$result = $cprValidator->containsCpr('My id is 0101601234');
```

## Check code style

```sh
docker-compose run phpfpm composer run check-coding-standards/php-cs-fixer
```

## Run unit tests

```sh
docker-compose run phpfpm composer run phpunit
```
