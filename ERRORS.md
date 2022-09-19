# Errors

All notable errors whilst developing `yodlee-php-api` will be documented in this file.

## Yodlee API Errors

Search for a new error like this:

https://developer.yodlee.com/search?search_term=Y020

---

Error

Maximum Thresholds for the day have reached. Please try after 24 hours.

Where encountered

After linking a specific bank ~ 5 times in around 8 hours

---

Error Y023

401	Y023	Token has expired	The Authorization token has expired. Create a fresh valid access token.

Where Encountered

Using /fastlink.php this was hidden in inspect element. From the fastlink.php code is seems obvious that a new fresh JWT token needs to be generated.

---

Error Y019

401	Y019	Issuer is either locked or deleted	You have provided an issuer or API key that is either locked or deleted.

Where encountered

Upon importing Yodlee accounts from staging endpoint during a Laravel migration:
Yodlee get endpoint: https://stage.api.yodlee.uk/ysl/accounts

---

401	Y020	Invalid token in Authorization header	The Authorization token is invalid. Create a new valid Access Token.

Where encountered

After a long time using an application on local, running art yodlee:get-accounts first didn't have the right API key and thereafter it wasn't liking the JWT token # yodlee-api

Be mindful of `Invalid token in authorization header` because it could indicate a generic problem with your user accessing the API, e.g. when they have been deleted.

---

Y025

Invalid token. This endpoint does not accept a user-specific token. Provide a token without any user identifier

Where encountered

Setting up a new post request for registerUser() and just using the default header that has a JWTToken

---

Y902

Oops some issue at our end

Where encountered

Sending registerUser() with a blank username

---

Y800

Invalid value for userParam

Where encountered

Trying to create a new user after having deleted the main user

---

## Laravel Errors

RuntimeException: A facade root has not been set.

Where encountered

After making changes with signatures and refactoring old code the tests broken down

The problem was actually using Log:: in this standalone package - removed it.

---

Call to a member function tap() on null

Forget to return from $this->hasMany? Must be return $this->hasMany!

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