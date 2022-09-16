# Changelog

All notable changes to `yodlee-php-api` will be documented in this file.

## 0.0.24 - 2022-09-16

- Added deleteAccount & deleteProviderAccount API calls

## 0.0.23 - 2022-09-04

- New method getNextTransactionUrl

## 0.0.22 - 2022-08-28

- Additional refinement of register user command error checking

## 0.0.21 - 2022-08-28

- Register user command must decode json before checking for errors

## 0.0.20 - 2022-08-28

- Added dataExtracts() method

## 0.0.19 - 2022-08-27

- Fix deleteUser commmand should be unregisterUser

## 0.0.18 - 2022-08-25

- Add commands to subscribe, fetch, and unsubscribe to event notifications for DATA_UPDATES

## 0.0.17 - 2022-08-25

- Refactor all tests to use Laravel's HTTP client instead of custom get
- Remove dependency of FintechSystem/ApiHelper
- Update composer dependencies
- Create event subscription callback API
- Try code coverage with PhpUnit, but abandon
- Simplify tests setup

## 0.0.15 - 2022-08-23

- Fix `LaravelApiHelpersCommand` renaming problem

## 0.0.14 - 2022-08-23

This will be the last version on packagist as yodlee-php-api, the next version will be simply know as yodlee-api. Also moving over to a version that can use Laravel's HTTP facade as this will greatly simplify testing.

- Renamed LaravelApiHelpers na ApiHelpers
- Added `EVENT_CALLBACK_URL` URL for callback in .env to aid in testing
- Added createSubscriptionNotificationEvent, getSubscribedNotificationEvents and deleteNotificationSubscription API calls and rudimentary tests
- Added API post and delete methods
- Created a callback url for subscription notification events and added to service provider
- In initialize_app, specify /storage as path to keys
- Simplified .gitignore by also renaming key files
- Added getSubscribedNotificationEvents

## 0.0.13 - 2022-08-20

- Updated tests to use `/storage` and added instructions on read about using this folder
- Changed all documentation URLs in the API to reflect new #tag location
- Move order of code around in API so that methods are alphabetical and not often used method are at the bottom
- General cleanup of comments
- Additional instructions in readme about installation and testing
- Updated composer dependencies
- Added a test to "Get All Users" which only seems to return the first user

## 0.0.11 - 2021-10-22

- changed default for GetTransactions from 30 to 90 days

## 0.0.6 - 0.0.10

- various storage issues with private key and finding it standalone versus when using Laravel

## 0.0.5 - 2021-10-17

- refactor and cleanup, remove more legacy methods especially for accounts and transactions

## 0.0.4 - 2021-10-17

- fix problem whereby get provider accounts didn't take a username

## 0.0.3 - 2021-10-17

- added new tests to register and delete users
- removed test to get accounts as 'default-user' doesn't have any accounts linked via Fastlink
- remove Log:: because it was giving facade error
- documented facade error in the README, and then split off error into an ERRORS.md

## 0.0.2 - 2021-10-16

- first working version

## 0.0.1 - 2021-10-12

- initial release as a package

## 0.0.0 - 2021-10-10

- pre-release
- removed "dd" depenency and added orchestra testbench instead
- added *.json to .gitignore
- prototype converted from jacquestrdx123
