Fintech Systems Yodlee Implementation Library
=============================================

** THIS IS PROTOTYPE AND BETA SOFTWARE ** BE CAREFULL

First Steps
-----------
You need "/fastlink.php" to set up the link with the bank.

Need help?
----------

* Think of a search term, e.g. Y023
https://developer.yodlee.com/search?search_term=Y023

Workflow
--------

This is a basic overview of all the various security layers and workflow before banking data retrieval:

- First log in using cobrand details. This is cobrand_name, cobrand_login, and cobrand_password
-- These credentials are stored in the .env file
- Then you create cobrand session
- Then you create an API key in the cobrand session & also crease private key and public key using PHP security libraries included
- Then with the API you can create a JWT token - Yodlees supplies a PHP script where you pass API key and private key and then it happens

- Then with the JWT token;
- Then register user call user eugene API call user register - using POSTMAN once you have a user

- Then put all parameters, basically API key and private key
- Put that into sample app

- Type in Username eugene into, clicked go
- Then went into Fastlink - a modal popped up

- In Fastlink modal, there was no accounts
- Then clicked linked Account

- Then choose Standard Bank, log in, click link.

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

Roadmap
-------
- A basic Livewire front-end has live data

Yodlee Error Codes
------------------
Look them up so:
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
# yodlee-php-api
