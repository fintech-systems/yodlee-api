# Yodlee PHP API
![GitHub](https://img.shields.io/github/license/fintech-systems/yodlee-php-api)

*THIS IS PROTOTYPE AND ALPHA SOFTWARE** BE CAREFULL

Installation
------------
`composer require fintech-systems/yodlee-php-api`

Laravel Specific Config
-----------------------
To publish the config:

```
php artisan vendor:publish --tag=yodlee-config
```

Local Development
-----------------
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
yodlee:provider-accounts              Retrieve a list of Yodlee provider accounts
yodlee:providers                      Retrieve a list of Yodlee providers
```

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

Change Log
----------

10 October 2021

- Removed "dd" depenency and added orchestra testbench instead
- Added *.json to .gitignore

Need help?
----------

* Think of a search term, e.g. Y023
https://developer.yodlee.com/search?search_term=Y023

Yodlee Error Codes
------------------
Look errors up same as above:

https://developer.yodlee.com/search?search_term=Y020

Error Y023

401	Y023	Token has expired	The Authorization token has expired. Create a fresh valid access token.

Where Encountered

Using /fastlink.php this was hidden in inspect element. From the fastlink.php code is seems obvious that a new fresh JWT token needs to be generated.

Error Y019

401	Y019	Issuer is either locked or deleted	You have provided an issuer or API key that is either locked or deleted.

Where encountered

Upon importing Yodlee accounts from staging endpoint during a Laravel migration:
Yodlee apiGet endpoint: https://stage.api.yodlee.uk/ysl/accounts

401	Y020	Invalid token in Authorization header	The Authorization token is invalid. Create a new valid Access Token.

Where encountered

After a long time using Bankystatement on local, running art yodlee:get-accounts first didn't have the right API key and now apparently it's not like the JWT token# yodlee-php-api

# yodlee-php-api

