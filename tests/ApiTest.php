<?php

namespace FintechSystems\Api\Tests;

use FintechSystems\YodleeApi\Enums\SubscriptionNotificationEvent;
use FintechSystems\YodleeApi\Tests\Setup;
use FintechSystems\YodleeApi\YodleeApi;
use Yodlee;

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
    public function registering_the_same_user_again_gives_a_sensible_message()
    {
        $yodlee = new YodleeApi($this->client());

        $response = $yodlee->registerUser(
            'test-user',
            'test@example.com'
        );

        $this->assertFalse($response->successful());
        $this->assertTrue($response->failed());
        $this->assertArrayHasKey('errorMessage', $response->json());
        $this->assertObjectHasAttribute('errorMessage', json_decode($response->body()));
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
     * Data Extracts.
     *
     * https://developer.yodlee.com/api-reference#tag/DataExtracts
     *
     * @test
     */
    public function it_can_call_a_data_extracts_url_as_provided_by_subscription_event_notification()
    {
        $url = 'dataExtracts/userData?fromDate=2022-08-27T08:41:20Z&toDate=2022-08-27T08:56:20Z&loginName=1edaf370-52a6-4cd1-8c3d-2c36f7ae6932';

        $yodlee = new YodleeApi($this->client());

        $response = $yodlee->dataExtracts($url);

        // TBA proper HTTP API testing
        ray($response->json());
    }

    /**
     * @test
     */
    public function it_can_get_transactions()
    {
        // Http::fake();

        // Http::withHeaders([
        //     'X-First' => 'foo',
        // ])->post('http://example.com/users', [
        //     'name' => 'Taylor',
        //     'role' => 'Developer',
        // ]);

        // : /transactions?fromDate=2022-06-03&skip=500&top=500;rel=next, /transactions/count?fromDate=2022-06-03;rel=count
        // Http::assertSent(function (Request $request) {
        //     return $request->hasHeader('X-First', 'foo') &&
        //         $request->url() == 'http://example.com/users' &&
        //         $request['name'] == 'Taylor' &&
        //         $request['role'] == 'Developer';
        // });

        $yodlee = new YodleeApi($this->client());

        $response = $yodlee->getTransactions('ed1e2182');

        dd($response->headers()['Link'][0]);

        $url = '';
    }

    /**
     * @test
     */
    public function it_can_strip_the_next_transactions_url_for_only_whats_needed()
    {
        $link = ': /transactions?fromDate=2022-03-08&skip=500&top=500;rel=next, /transactions/count?fromDate=2022-03-08;rel=count';

        $actual = (new YodleeApi($this->client()))->getNextTransactionUrl($link);

        $this->assertEquals('transactions?fromDate=2022-03-08&skip=500&top=500', $actual);

        $link = ': /transactions?fromDate=2022-03-08&top=500;rel=previous, /transactions?fromDate=2022-03-08&skip=1000&top=500;rel=next, /transactions/count?fromDate=2022-03-08;rel=count';

        $actual = (new YodleeApi($this->client()))->getNextTransactionUrl($link);

        $this->assertEquals('transactions?fromDate=2022-03-08&skip=1000&top=500', $actual);
    }

    /**
     * @test
     */
    public function it_can_delete_an_account()
    {
        $username = 'ebdd51a4';

        $accountId = '10123816';

        $yodlee = new YodleeApi($this->client());

        $response = $yodlee->deleteAccount($username, $accountId);

        $this->assertEquals(204, $response->status());
    }
}
