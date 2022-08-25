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
vendor/bin/phpunit --filter it_can_generate_a_jwt_token tests/ApiTest.php
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

## Common Development Errors

Please see [ERRORS](ERRORS.md) for a list of commonly found errors whilst using the Yodlee API.

## .gitignore Security Notice

Storing private or public keys in a source code repository is a massive security risk.

The `.gitignore` file supplied with this repository contains the following:

```
/storage/*.pem
```

Under no circumstances upload your keys to a public (or private) repo.

Additionally `*.json` ignores are there because the Api Helper has the ability to cache output and those files should also be ignored.

```
*.example.json
*.cache.json
```

Testing Fastlink
----------------
Use these credentials to test Fastlink:

Username (case-sensitive): DAGtest.site16441.1
Password: site16441.1

How Yodlee Works (high level overview)
--------------------------------------
https://av.developer.yodlee.com/

A video:

https://developer.yodlee.com/vqs

Registering a user
------------------
https://av.developer.yodlee.com/#c8fbfce3-bc51-4aeb-a795-301086b918d4

First Steps
-----------
You need "/fastlink.php" to set up the link with the bank.

Workflow
--------

This is a basic overview of all the various security layers and workflow before provider data retrieval:

- First log in using the cobrand details. This is cobrand_name, cobrand_login, and cobrand_password
  - These credentials are stored in the .env file

- Then you create cobrand session

- Then you create an API key in the cobrand session & also crease private key and public key using PHP security libraries included

- Then with the API you can create a JWT token - Yodlees supplies a PHP script where you pass API key and private key and then it happens

- Then with the JWT token;

- Then register user call user xyz API call user register - using POSTMAN once you have a user

- Then put all parameters which is the API key and private key

- Put that into sample app

- Type in Username xyz into, clicked go

- Then went into Fastlink - a modal popped up

- In Fastlink modal, there was no accounts

- Then clicked linked Account

- Then choose A provider's name, log in, click link.

Instructions
------------

To start off run initialize_app.php

This will provide you with variables required for the test phases

*NB* Save the output to a file for later use *NB*

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Need Help?

The Yodlee Developer's API Reference can be found here:

https://developer.yodlee.com/api-reference

I'm developing this API on my own time for a larger project but if you reach out I might be able to help or prioritize features.

eugene@fintechsystems.net
+27823096710