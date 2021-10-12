<?php

namespace FintechSystems\YodleeApi;

class Crypt
{		
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
