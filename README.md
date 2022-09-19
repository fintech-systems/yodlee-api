# Yodlee API

[![Latest Stable Version](https://poser.pugx.org/fintech-systems/yodlee-php-api/v/stable?format=flat-square)](https://packagist.org/packages/fintech-systems/yodlee-api)
![GitHub](https://img.shields.io/github/license/fintech-systems/yodlee-api)

## Installation

### Install the package via composer:

```bash
composer require fintech-systems/yodlee-api
```

### Store private and public keys

Create a directory `/storage` and copy your private key to `/storage/private-key.pem`.
If you want to run the full testsuite, also copy `public-key.pem` to `/storage`.

### Set up the environment file

Copy `.env.example` file to `.env` and complete the details.

## Contents of .env

```
YODLEE_COBRAND_NAME=
YODLEE_COBRAND_LOGIN=
YODLEE_COBRAND_PASSWORD=
YODLEE_API_URL=
YODLEE_API_KEY=
YODLEE_USERNAME=
```

## Commands

List of Commands
----------------

Display API keys:

```
✗ php artisan yodlee:api-key
+-----------------------------------------------+-------------+
| key                                           | createdDate |
+-----------------------------------------------+-------------+
| 00000000-00000000-0000-0000-0000-000000000000 | 2021-05-06  |
+-----------------------------------------------+-------------+
```

Console Commands
----------------

The console commands contains a subset of the main API methods.

```
yodlee:accounts                       Fetch a list of Yodlee accounts
yodlee:api-key                        Fetch a list of Yodlee API keys
yodlee:delete-user                    Delete an existing Yodlee user
yodlee:event-subscriptions            Fetch a list of subscribed notification events
yodlee:get-user                       Fetch details about a Yodlee user
yodlee:providers                      Fetch a list of Yodlee providers
yodlee:provider-accounts              Fetch a list of Yodlee provider accounts
yodlee:register-user                  Register a new Yodlee user
yodlee:subscribe                      Subscribe to DATA_UPDATES event notifications
yodlee:transactions                   Fetch a list of Yodlee transactions for a user
yodlee:unsubscribe                    Unsubscribe from DATA_UPDATES event notifications
```

## Testing

Test examples:

```bash
vendor/bin/phpunit
vendor/bin/phpunit --testdox
vendor/bin/phpunit tests/ApiTest.php
vendor/bin/phpunit --filter it_can_generate_a_jwt_token tests/
ApiTest.php
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html=tests/coverage-report
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Laravel Specific Config

To publish the config:

```
php artisan vendor:publish --tag=yodlee-config
```

## Local Development

- Ensure your `private-key.pem` and `public-key.pem` keys are stored in /storage

The event subscription callback URL will normally be `https://app_url/api/v1/event`.

If you're doing local development, add a temporary URL to the `.env` file, e.g:
`EVENT_CALLBACK_URL=my-app.eu-1.sharedwithexpose.com/api/v1/event`

The above example assumes you're using Expose. The start Expose with this URL with Laravel Valet, do this:

`expose share --subdomain=my-app --server=eu-1 http://my-app.test`

If you're testing with an existing project, then update composer.json in the existing project to require the file like so:

```
"repositories": [
        ...
        ,
        {
            "type": "path",
            "url": "../yodlee-api"
        }
    ],
```

Then update composer:

```
  composer require fintech-systems/yodlee-api:dev-main
...
  - Upgrading fintech-systems/yodlee-api (v0.0.17 => dev-main)
...
  - Removing fintech-systems/yodlee-api (v0.0.17)
  - Installing fintech-systems/yodlee-api (dev-main): Symlinking from ../yodlee-api
Generating optimized autoload files
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Need Help?

### Yodlee ERD

To get a high level overview of Yodlee's data structure, see here:

https://developer.yodlee.com/docs/api/1.1/Data_Model

### Yodlee API Reference

The Yodlee Developer's API Reference can be found here:

https://developer.yodlee.com/api-reference

I'm developing this API on my own time for a larger project but if you reach out I might be able to help or prioritize features.

eugene@fintechsystems.net
+27823096710
