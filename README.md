# PHP Curl Class

This library provides an object-oriented wrapper of the PHP cURL extension.

If you have questions or problems with installation or usage [create an Issue](https://github.com/firmaprofesional/curl/issues).

## Installation

In order to install this library via composer run the following command in the console:

```sh
composer require firmaprofesional/curl
```

or add the package manually to your composer.json file in the require section:

```json
"firmaprofesional/curl": "^0.3"
```

## Usage examples

```php
$curl = new CurlService();
$curlConfig = new CurlConfig();
$curlConfig->setCurlUrl('www.example.com');
$curl->configure($curlConfig);
$curl->send();
```

basic authentication
```php
$curl = new CurlService();
$curlConfig = new CurlConfig();
$curlConfig->setCurlUrl('www.example.com');
$curlConfig->setUsername('user');
$curlConfig->setUserPassword('password');
$curl->configure($curlConfig);
$curl->send();
```

post fields
```php

$curl = new CurlService();
$curlConfig = new CurlConfig();
$curlConfig->setCurlUrl('www.example.com');
$curlConfig->setMethodPOST();
$data_string = json_encode(array('data'));
$curlConfig->setHttpHeader(
    array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string),
    )
);

$curlConfig->setData($data_string);

$curl->configure($curlConfig);
$curl->send();
```

post fields with bearer token authentication
```php

$curl = new CurlService();
$curlConfig = new CurlConfig();
$curlConfig->setCurlUrl('www.example.com');
$curlConfig->setMethodPOST();
$data_string = json_encode(array('data'));
$curlConfig->setHttpHeader(
    array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string),
        'Authorization: Bearer bearertoke'
    )
);

$curlConfig->setData($data_string);

$curl->configure($curlConfig);
$curl->send();
```

enable verbose mode will log on tmp path
```php

$curl = new CurlService();
$curlConfig = new CurlConfig();
$curlConfig->setCurlUrl('www.example.com');
$curlConfig->setVerbose(true);
$curl->configure($curlConfig);
$curl->send();
```

set timeout in seconds
```php

$curl = new CurlService();
$curlConfig = new CurlConfig();
$curlConfig->setCurlUrl('www.example.com');
$curlConfig->setTimeout(10);
$curl->configure($curlConfig);
$curl->send();
```


## Testing

In order to test the library:

1. Create a fork
2. Clone the fork to your machine
3. Install the depencies `composer install`
4. Run the unit tests `./vendor/phpunit/bin/phpunit -c phpunit.xml --testsuite general`