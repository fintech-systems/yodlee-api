<?php

namespace FintechSystems\YodleeApi;

use Carbon\Carbon;
use Exception;
use FintechSystems\LaravelApiHelpers\Api;
use FintechSystems\YodleeApi\Contracts\BankingProvider;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class YodleeApi implements BankingProvider
{
    private $privateKey = '/storage/private-key.pem';

    private $cobrandName;

    private $apiUrl;

    private $apiKey;

    private $username;

    private $header;

    public function __construct(array $client)
    {
        $this->cobrandName = $client['cobrand_name'];
        $this->apiUrl = $client['api_url'];
        $this->apiKey = $client['api_key'];
        $this->username = $client['username'];

        $cwd = str_replace('/public', '', getcwd());
        $this->privateKey = file_get_contents(
            $cwd . $this->privateKey
        );

        $this->header = [
            'Api-Version' => '1.1',
            'Cobrand-Name' =>  $this->cobrandName,
            'Authorization' => 'Bearer ' . $this->generateGenericJwtToken(),
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Build a get command
     * 
     * When no username, build header using the default username as defined in the .env file
     */
    public function get($endpoint, $username = null)
    {
        if ($username == null) {
            $token = $this->generateJwtToken($this->username);
        } else {
            $token = $this->generateJwtToken($username);
        }

        $header = [
            'Api-Version' => 1.1,
            'Authorization' => 'Bearer ' . $token,
            'Cobrand-Name' => $this->cobrandName,
            'Content-Type' => 'application/json',
        ];

        return Http::withHeaders($header)->get(
            $this->apiUrl . $endpoint,            
        );
    }

    public function apiPost($endpoint, $data)
    {
        $token = $this->generateJwtToken($this->username);

        ray("Defaulting to stored username '$this->username' for post request")->orange();

        ray("The endpoint for this post is $endpoint");

        ray('The data is ', $data);

        $api = new Api();

        $response = $api->post(
            $this->apiUrl . $endpoint,
            $data,
            [
                'Api-Version: 1.1',
                'Authorization: Bearer ' . $token,
                'Cobrand-Name: ' . $this->cobrandName,
                'Content-Type: application/json',
            ]
        );

        return $response;
    }

    /**
     * Add a new user (consumer) to the system.
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/User/operation/registerUser
     */
    public function registerUser(
        $loginName,
        $email,
        $currency = 'ZAR',
        $timeZone = 'GMT+2',
        $dateFormat = 'yyyy-MM-dd',
        $locale = 'en_ZA'
    ) {
        $postFields = '
        {
            "user": {
              "loginName":"' . $loginName . '",              
              "email": "' . $email . '",
              "preferences": {                
                "currency": "' . $currency . '",
                "timeZone": "' . $timeZone . '",
                "dateFormat": "' . $dateFormat . '",
                "locale": "' . $locale . '"
              }              
            }
          }
        ';

        $url = $this->apiUrl . 'user/register';

        return Http::withHeaders(
            $this->header
        )->post($url, json_decode($postFields, true));
    }

    /**
     * Delete a user (Yodlee consumer). This is also called unregister a user.
     * 
     * Bearer is a JWT token of the user to be deleted
     *
     * On success this method returns an empty string but on the CURL request is generates a 204 (Success without content)
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/User/operation/userLogout
     */
    public function unregisterUser($user)
    {
        $url = $this->apiUrl . 'user/unregister';

        $header = [
            'Api-Version' => '1.1',
            'Authorization' => 'Bearer ' . $this->generateJwtToken($user),
            'Cobrand-Name' => $this->cobrandName,
            'Content-Type' => 'application/json',
        ];

        return Http::withHeaders(
            $header
        )->delete($url);
    }

    /**
     * Configs Operation createSubscriptionNotificationEvent.
     *
     * https://developer.yodlee.com/api-reference#tag/Configs/operation/createSubscriptionNotificationEvent
     */
    public function createSubscriptionNotificationEvent($eventName, $callbackUrl = '')
    {
        if (env('YODLEE_EVENT_CALLBACK_URL')) {
            $callbackUrl = env('YODLEE_EVENT_CALLBACK_URL');
        } else {
            $callbackUrl = config('app.url') . '/yodlee/event';
        }
        
        $data = [
            'event' => [
                'callbackUrl' => $callbackUrl
            ]
        ];

        // $data = '{ "event": {
        //             "callbackUrl":"' . $callbackUrl . '"
        //         }}';

        $url = $this->apiUrl . "/configs/notifications/events/$eventName";

        return Http::withHeaders($this->header)
            ->post($url, $data);
    }

    /**
     * getSubscribedNotificationEvents.
     *
     * https://developer.yodlee.com/api-reference#tag/Configs/operation/getSubscribedNotificationEvents
     */
    public function getSubscribedNotificationEvents()
    {
        return $this->get('/configs/notifications/events');
    }

    /**
     * deleteNotificationSubscription
     * 
     * https://developer.yodlee.com/api-reference#tag/Configs/operation/deleteSubscribedNotificationEvent
     */
    public function deleteNotificationSubscription($eventName)
    {
        $url = $this->apiUrl . "/configs/notifications/events/$eventName";

        return Http::withHeaders($this->header)
            ->delete($url);
    }


    /**
     * Get all accounts of a user held at the providers, i.e. all their bank accounts.
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/Accounts/operation/getAllAccounts
     */
    public function getAccounts($user = null)
    {
        return $this->get('accounts', $user);
    }

    /**
     * This endpoint provides the list of API keys that exist for a customer.
     *
     * You can use one of the following authorization methods to access this API:
     *  - cobsession
     *  - JWT token
     *
     * Not available in developer sandbox environment.
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/Auth/operation/getApiKeys
     */
    public function getApiKeys()
    {
        return $this->get('auth/apiKey');
    }

    /**
     * Get a list of all the providers (financial institutions) that have been added by Yodlee based on a country code.
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/Providers/operation/getAllProviders
     */
    public function getProviders($priority = 'cobrand', $iso = 'ZA')
    {
        return $this->get("providers?priority=$priority&countryISOCode=$iso");
    }

    /**
     * Get a list of provider (banking) accounts added by the user.
     *
     * This includes all failed and successfully added provider accounts.
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/ProviderAccounts
     */
    public function getProviderAccounts($username)
    {
        return $this->get('providerAccounts', $username);
    }

    /**
     * Get all transactions for the last 90 days.
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/Transactions/operation/getTransactions
     */
    public function getTransactions($username, $fromDate = null)
    {
        $fromDate == null
            ? $fromDate = Carbon::now()->subDays(90)->format('Y-m-d')
            : $fromDate = $fromDate;

        $url = "transactions?fromDate=$fromDate";

        return $this->get($url, $username);
    }

    /**
     * Get transactions for the last 90 days for a specific account only.
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/Transactions/operation/getTransactions
     */
    public function getTransactionsByAccount($username, $accountId, $fromDate = null)
    {
        $fromDate == null
            ? $fromDate = Carbon::now()->subDays(90)->format('Y-m-d')
            : $fromDate = $fromDate;

        $url = "transactions?accountId=$accountId&fromDate=$fromDate";

        return $this->get($url, $username);
    }

    /**
     * The get user details service is used to get the user profile information
     * and the application preferences set at the time of user registration.
     *
     * https://developer.yodlee.com/api-reference#tag/User/operation/getUser
     */
    public function getUser($username)
    {
        return $this->get('user', $username);
    }

    /**
     * This endpoint is used to generate an API key.
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/Auth/operation/generateApiKey
     */
    public function generateApiKey($url, $cobrandArray, $publicKey)
    {
        $curl = curl_init();

        $publicKey = preg_replace('/\n/', '', $publicKey);

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
        		"publicKey": "' . $publicKey . '"
  			}',
            CURLOPT_HTTPHEADER => [
                'Api-Version: 1.1',
                'Authorization: cobSession=' . $cobrandArray['cobSession'],
                'Cobrand-Name: ' . $cobrandArray['cobrandName'],
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $object = json_decode($response);

        // If a key doesn't exist after the API call, then just return the response which contains the error
        return $object->apiKey['0']->key ?? $response;
    }

    /**
     * Generate a JWT token for a user
     */
    public function generateJwtToken($user)
    {
        $payload = [
            'sub' => $user,
            'iss' => $this->apiKey,
            'iat' => time(),
            'exp' => time() + 1800,
        ];

        return JWT::encode($payload, $this->privateKey, 'RS512');
    }


    /**
     * Generic tokens are used for global API calls that does not pertain to a user.
     *
     * The key format must be precise other you may get this error:
     *  openssl_sign(): Supplied key param cannot be coerced into a private key
     *
     * Yodlee documentation overview of JWT:
     * https://developer.yodlee.com/docs/overview-json-web-tokens
     * https://developer.yodlee.com/docs/api/1.1/getting-started-with-jwts
     */
    public function generateGenericJwtToken()
    {
        $payload = [
            'iss' => $this->apiKey,
            'iat' => time(),
            'exp' => time() + 1800,
        ];

        return JWT::encode($payload, $this->privateKey, 'RS512');
    }

    public function getCobSession($url, $cobrandArray)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
        		"cobrand":      {
					"cobrandLogin": "' . $cobrandArray['cobrandLogin'] . '",
					"cobrandPassword": "' . $cobrandArray['cobrandPassword'] . '"
         		}
    		}',
            CURLOPT_HTTPHEADER => [
                'Api-Version: 1.1',
                'Cobrand-Name: '.$cobrandArray['cobrandName'],
                'Content-Type: application/json',
                'Cookie: JSESSIONID=xxx', // REDACTED TODO Research
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $object = json_decode($response);

        return $object->session->cobSession;
    }
}
