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
Yodlee apiGet endpoint: https://stage.api.yodlee.uk/ysl/accounts

---

401	Y020	Invalid token in Authorization header	The Authorization token is invalid. Create a new valid Access Token.

Where encountered

After a long time using Bankystatement on local, running art yodlee:get-accounts first didn't have the right API key and now apparently it's not like the JWT token# yodlee-php-api

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
