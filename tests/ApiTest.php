<?php

namespace FintechSystems\Api\Tests;

use FintechSystems\YodleeApi\Tests\Setup;
use FintechSystems\YodleeApi\YodleeApi;

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
    public function it_can_generate_a_jwt_token()
    {
        $yodlee = new YodleeApi($this->getClient());

        $token = $yodlee->generateGenericJWTToken();

        $this->assertGreaterThan(498, strlen($token));
    }

    /**
     * @test
     */
    public function api_keys_can_be_retrieved()
    {
        $yodlee = new YodleeApi($this->getClient());

        $result = $yodlee->apiGet('auth/apiKey');

        $result = json_decode($result, true); // true turns object to associative array;

        $this->assertEquals(5, count($result['apiKey']));
    }

    /**
     * @test
     *
     * FYI you cannot generate more than 5 API keys otherwise you get this:
     *    "The maximum number of apiKey permitted is 5"
     */
    public function trying_to_generate_a_sixth_key_using_public_key_produces_an_error()
    {
        $client = $this->getClient();

        $yodlee = new YodleeApi($client);

        $cobrandArray = [
            'cobrandName'     => $client['cobrand_name'],
            'cobrandLogin'    => $client['cobrand_login'],
            'cobrandPassword' => $client['cobrand_password'],
        ];

        $loginUrl = $client['api_url'].'cobrand/login';

        $yodlee = new YodleeApi($client);

        $cobrandArray['cobSession'] = $yodlee->getCobSession(
            $loginUrl,
            $cobrandArray
        );

        $apiKeyUrl = $client['api_url'].'auth/apiKey';

        ray($apiKeyUrl);

        $publicKey = file_get_contents('public.pem');

        $key = $yodlee->generateAPIKey(
            $apiKeyUrl,
            $cobrandArray,
            $publicKey,
        );

        $key = json_decode($key, true);

        $this->assertEquals('The maximum number of apiKey permitted is 5', $key['errorMessage']);
    }

    /** @test */
    public function it_can_register_a_new_user()
    {
        $yodlee = new YodleeApi($this->getClient());

        $result = $yodlee->registerUser('test-user', 'test@example.com');

        $this->assertObjectHasAttribute('id', $result->user);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        $yodlee = new YodleeApi($this->getClient());

        $result = $yodlee->deleteUser('test-user');

        $this->assertEmpty('', $result);
    }

    /**
     * Test disabled because the .env 'default-user' doesn't have any accounts linked.
     *
     * @test
     */
    // public function it_can_retrieve_the_total_number_of_yodlee_accounts()
    // {
    //     $yodlee = new YodleeApi($this->getClient());

    //     $result = $yodlee->getAccounts();

    //     $this->assertEquals(4, count($result->account));
    // }
}
