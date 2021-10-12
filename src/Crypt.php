<?php

namespace FintechSystems\YodleeApi;

use \Firebase\JWT\JWT;

class Crypt
{	
	// private $privateKeyFilename = 'private.pem';
		
	// public function generateJWTToken()
	// {
	// 	$api_key    = $_ENV['YODLEE_API_KEY'];
	// 	$username   = $_ENV['YODLEE_USERNAME'];
	// 	$privateKey = file_get_contents($this->privateKeyFilename);
		
	// 	$payload = [
	// 		"iss" => $api_key,
	// 		"iat" => time(),
	// 		"exp" => time() + 1800,
	// 		'sub' => $username,
	// 	];
						
	// 	return JWT::encode($payload, $privateKey, 'RS512');
	// }

	public function generateRSAKey()
	{
		$config = [
			"digest_alg"       => "sha512",
			"private_key_bits" => 2048,
			"private_key_type" => OPENSSL_KEYTYPE_RSA,
		];
		$res = openssl_pkey_new($config);

		// Get private key  
		openssl_pkey_export($res, $privkey);

		// Get public key  
		$pubkey = openssl_pkey_get_details($res);
		$pubkey = $pubkey["key"];

		$rsa_key = [
			"public_key"  => $pubkey,
			"private_key" => $privkey,
		];

		return $rsa_key;
	}
}
