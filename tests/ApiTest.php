<?php

namespace FintechSystems\Api\Tests;

use FintechSystems\YodleeApi\Enums\SubscriptionNotificationEvent;
use FintechSystems\YodleeApi\Tests\Setup;
use FintechSystems\YodleeApi\YodleeApi;

class ApiTest extends Setup
{
    /** @test */
    public function it_can_read_an_api_url_parameter_from_the_testing_environment_and_assign_it_to_an_array()
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
        $yodlee = new YodleeApi($this->client());

        $token = $yodlee->generateGenericJwtToken();

        $this->assertEquals(499, strlen($token));
    }

    /**
     * @test
     */
    public function api_keys_can_be_retrieved()
    {
        $yodlee = new YodleeApi($this->client());

        $response = $yodlee->get('auth/apiKey');

        $this->assertGreaterThan(0, count($response->json()['apiKey']));
    }

    /**
     * @test
     *
     * Test you cannot generate more than 5 API keys otherwise you get this:
     *    "The maximum number of apiKey permitted is 5"
     */
    public function trying_to_generate_a_sixth_key_using_public_key_produces_an_error()
    {
        $client = $this->client();

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
        $yodlee = new YodleeApi($this->client());

        $response = $yodlee->registerUser('test-user', 'test@example.com');

        $this->assertArrayHasKey('user', $response->json());
    }

    /** @test */
    public function it_can_unregister_a_user()
    {
        $yodlee = new YodleeApi($this->client());

        $response = $yodlee->unregisterUser('test-user');

        $this->assertEquals(204, $response->getStatusCode());
    }

    /**
     * @test
     *
     * Get A User Test
     *
     * Please note there is no API to get all users
     */
    public function it_can_get_a_user()
    {
        $yodlee = new YodleeApi($this->client());

        $response = $yodlee->getUser($this->client()['username']);

        $this->assertArrayHasKey('user', $response->json());
    }

    /**
     * @test
     */
    public function it_can_subscribe_to_a_data_updates_notification_event()
    {
        $yodlee = new YodleeApi($this->client());

        $response = $yodlee->createSubscriptionNotificationEvent(
            SubscriptionNotificationEvent::DATA_UPDATES
        );

        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_can_get_subscribed_notification_events()
    {
        $yodlee = new YodleeApi($this->client());

        $response = $yodlee->getSubscribedNotificationEvents();

        $this->assertArrayHasKey('event', $response->json());
    }

    /**
     * @test
     */
    // public function it_can_delete_a_refresh_notification_event()
    // {
    //     $yodlee = new YodleeApi($this->client());

    //     $response = $yodlee->deleteNotificationSubscription(
    //         SubscriptionNotificationEvent::DATA_UPDATES
    //     );

    //     $this->assertEquals(204, $response->getStatusCode());
    // }

    /**
     * @test
     */
    public function it_can_handle_an_incoming_user_data_update_event()
    {
       //
    }

    // 

    /**
     * Data Extracts
     * 
     * https://developer.yodlee.com/api-reference#tag/DataExtracts
     * 
     * @test
     */
    public function it_can_call_a_data_extracts_url_as_provided_by_subscription_event_notification()
    {
       //
    }
}
