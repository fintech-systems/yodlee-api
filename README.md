# Yodlee PHP API

[![Latest Stable Version](https://poser.pugx.org/fintech-systems/yodlee-php-api/v/stable?format=flat-square)](https://packagist.org/packages/fintech-systems/yodlee-php-api)
![GitHub](https://img.shields.io/github/license/fintech-systems/yodlee-php-api)

*THIS IS PROTOTYPE AND ALPHA SOFTWARE** BE CAREFULL

## Installation

You can install the package via composer:

```bash
composer require fintech-systems/yodlee-php-api
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Common Development Errors

Please see [ERRORS](ERRORS.md) for a list of commonly found errors whilst using the Yodlee API.

## Laravel Specific Config

To publish the config:

```
php artisan vendor:publish --tag=yodlee-config
```

## Local Development

- Ensure a private key named private.pem is stored in the root directory of the application.

Security Warning
================

Storing a private key in public source code repository is a huge security risk.
Ensure the `.gitignore` file contains at least the following:

```
*.pem
*.example.json
*.cache.json
```

The `*.json` ignores are there because some Laravel Artisan commands have the ability to cache and those files should also be ignored.

Commands
========

Numerous Laravel Artisan commands have the ability to cache API requests.

Note: This is a security risk if your .gitignore is not setup correctly. See the section `Security Warning`

Display all API keys:

```
✗ art yodlee:api-key
+-----------------------------------------------+-------------+
| key                                           | createdDate |
+-----------------------------------------------+-------------+
| 00000000-00000000-0000-0000-0000-000000000000 | 2021-05-06  |
+-----------------------------------------------+-------------+
```

Provider Accounts
-----------------

```
yodlee:accounts                       Retrieve a list of Yodlee accounts
yodlee:api-key                        Retrieve a list of Yodlee API keys
yodlee:delete-user                    Delete a Yodlee user
yodlee:get-user                       Retrieve details about a Yodlee user
yodlee:provider-accounts              Retrieve a list of Yodlee provider accounts
yodlee:providers                      Retrieve a list of Yodlee providers
yodlee:register-user                  Register a new Yodlee user
yodlee:transactions                   Retrieve a list of Yodlee transactions for a user
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

## Need Help?

The Yodlee Developer's API Reference can be found here:

https://developer.yodlee.com/api-reference

