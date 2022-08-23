<?php

require __DIR__.'/vendor/autoload.php';

include_once 'Yodlee.php';

include 'Crypt/RSA.php';

$rsa_key = Yodlee::generateRSAKey();

file_put_contents('storage/private-key.pem', $rsa_key['private_key']);

file_put_contents('storage/public-key.pem', $rsa_key['public_key']);

$url = 'https://stage.api.yodlee.uk/ysl/cobrand/login';

$cobrandArray = [
    'cobrandName' => 'xxx', // REDACTED
    'cobrandLogin' => 'xxx', // REDACTED
    'cobrandPassword' => 'xxx', // REDACTED
];

$cobrandArray['cobSession'] = Yodlee::getCobSession($url, $cobrandArray);

$url = 'https://stage.api.yodlee.uk/ysl/auth/apiKey';

$apiKey = Yodlee::generateAPIKey($url, $cobrandArray, $rsa_key);

  echo "--------------------\n";
  echo "--------Private----------\n";
  echo $rsa_key['private_key']."\n";
  echo "--------Public----------\n";
  echo $rsa_key['public_key']."\n";
  echo "--------APIKey----------\n";
  echo $apiKey."\n";
  echo "--------cobSession----------\n";
  echo $cobrandArray['cobSession']."\n";
