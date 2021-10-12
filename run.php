<?php

require __DIR__.'/vendor/autoload.php';

include_once 'Yodlee.php';

include 'Crypt/RSA.php';

$accounts_url = 'https://stage.api.yodlee.uk/ysl/accounts';

$api_key = 'xxx'; // REDACTED

$base_transactions_url = 'https://stage.api.yodlee.uk/ysl/transactions';

$private_key_file = 'private.pem';

$public_key_file = 'private.pem';

$transactions = [];

$username = 'xxx'; // REDACTED

$user_jwt_token = Yodlee::generateJWTToken($api_key, $private_key_file, $username);

$accounts = Yodlee::getUserAccounts($user_jwt_token, $accounts_url);

$from_date = '2021-10-01';

foreach ($accounts as $account) {
    $transactions_url = $base_transactions_url.'?fromDate='.$from_date.'+&accountId='.$account->id;

    $transactions[$account->id] = Yodlee::getTransactions($user_jwt_token, $transactions_url);
}

file_put_contents('transactions.json', json_encode($transactions));

dd($transactions);

?>

