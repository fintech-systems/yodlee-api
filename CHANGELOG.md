# Changelog

All notable changes to `yodlee-php-api` will be documented in this file.

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
