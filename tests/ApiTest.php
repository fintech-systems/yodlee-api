<?php

namespace FintechSystems\Api\Tests;

use FintechSystems\YodleeApi\YodleeApi;
use FintechSystems\YodleeApi\Tests\Setup;
use FintechSystems\YodleeApi\Enums\SubscriptionNotificationEvent;

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
        $env = $this->getClient();

        $yodlee = new YodleeApi($env);

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
     * Test you cannot generate more than 5 API keys otherwise you get this:
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

        $loginUrl = $client['api_url'] . 'cobrand/login';

        $yodlee = new YodleeApi($client);

        $cobrandArray['cobSession'] = $yodlee->getCobSession(
            $loginUrl,
            $cobrandArray
        );

        $apiKeyUrl = $client['api_url'] . 'auth/apiKey';

        ray($apiKeyUrl);

        $publicKey = file_get_contents('storage/public-key.pem');

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
    public function it_can_unregister_a_user()
    {
        $yodlee = new YodleeApi($this->getClient());

        $result = $yodlee->unregisterUser('test-user');

        $this->assertNull($result);
    }

    /**
     * @test
     *
     * Get All Users Test
     *
     * There is no API call to get all users but this probably exists in the front-end
     * but through trial and error we found that calling getUser returns the first
     * user, but when you specify the name you will get other users.
     */
    public function it_can_get_all_users()
    {
        $yodlee = new YodleeApi($this->getClient());

        $result = $yodlee->getAllUsers();

        $this->assertObjectHasAttribute('user', json_decode($result));
    }

    /**
     * @test
     */
    public function it_can_subscribe_to_a_data_updates_notification_event()
    {
        $yodlee = new YodleeApi($this->getClient());

        $result = $yodlee->createSubscriptionNotificationEvent(
            SubscriptionNotificationEvent::DATA_UPDATES
        );

        $this->assertEquals($result, '');
    }

    /**
     * @test
     */
    public function it_can_get_subscribed_notification_events()
    {
        $yodlee = new YodleeApi($this->getClient());

        $result = $yodlee->getSubscribedNotificationEvents();

        $this->assertObjectHasAttribute('event', json_decode($result));
    }

    /**
     * @test
     */
    public function it_can_delete_a_refresh_notification_event()
    {
        $yodlee = new YodleeApi($this->getClient());

        $result = $yodlee->deleteNotificationSubscription(
            SubscriptionNotificationEvent::DATA_UPDATES
        );

        $this->assertEquals($result, '');
    }
}
