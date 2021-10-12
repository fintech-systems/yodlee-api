<?php

namespace FintechSystems\Api\Tests;

use FintechSystems\YodleeApi\Crypt;
use FintechSystems\YodleeApi\YodleeApi;
use FintechSystems\YodleeApi\Tests\Setup;

class ApiTest extends Setup
{
    /** @test */
    public function it_can_read_an_api_url_from_an_env_testing_file_and_assign_it_to_an_array()
    {
        $this->init();

        $server = [
            'url' => $_ENV['YODLEE_API_URL'],
        ];

        $this->assertEquals('https://stage.api.yodlee.uk/ysl/', $server['url']);
    }

    /** @test */
    public function it_can_generate_a_jwt_token_from_an_existing_private_key()
    {
        $this->init();

        $crypt = new Crypt;

        $jwtToken = $crypt->generateJWTToken();

        ray($jwtToken);

        $this->assertEquals(528, strlen($jwtToken));
    }

    /**
     * @test
     */
    public function api_keys_can_be_retrieved()
    {
        $this->init();

        $crypt = new Crypt;

        $jwtToken = $crypt->generateJWTToken();

        $client = $this->getClient();

        $yodlee = new YodleeApi($client);

        $result = $yodlee->apiGet($jwtToken, 'auth/apiKey');

        $result = json_decode($result, true); // true turns object to associative array;

        $this->assertEquals(5, count($result['apiKey']));
    }

    /** 
     * @test
     * 
     * FYI you cannot generate more than 5 API keys otherwise you get this:
     *    "The maximum number of apiKey permitted is 5"
     * 
     */
    public function trying_to_generate_a_sixth_key_generates_an_error()
    {
        $this->init();

        $client = $this->getClient();

        $cobrandArray = array(
            "cobrandName"     => $client['cobrand_name'],
            "cobrandLogin"    => $client['cobrand_login'],
            "cobrandPassword" => $client['cobrand_password']
        );

        $loginUrl = $client['api_url'] . 'cobrand/login';

        $yodlee = new YodleeApi($client);

        $cobrandArray['cobSession'] = $yodlee->getCobSession(
            $loginUrl,
            $cobrandArray
        );

        $apiKeyUrl = $client['api_url'] . 'auth/apiKey';
        ray($apiKeyUrl);

        $publicKey = file_get_contents("public.pem");

        $key = $yodlee->generateAPIKey(
            $apiKeyUrl,
            $cobrandArray,
            $publicKey,
        );

        $key = json_decode($key, true);

        $this->assertEquals('The maximum number of apiKey permitted is 5', $key['errorMessage']);
    }

    /** @test */
    public function it_can_retrieve_the_total_number_of_yodlee_accounts()
    {
        $this->init();

        $client = $this->getClient();

        $crypt = new Crypt;

        $jwtToken = $crypt->generateJWTToken(
            $client['api_key'],
            $client['username'],
        );

        $yodlee = new YodleeApi($client);
        $result = $yodlee->getAccounts($jwtToken);

        $this->assertEquals(7, count($result->account));
    }
}
