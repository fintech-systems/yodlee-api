<?php

namespace FintechSystems\YodleeApi;

use Carbon\Carbon;
use Exception;
use FintechSystems\LaravelApiHelpers\Api;
use FintechSystems\YodleeApi\Contracts\BankingProvider;
use Firebase\JWT\JWT;

class YodleeApi implements BankingProvider
{
    private $privateKeyStoragePath = '/storage/';

    private $privateKeyFilename = 'private.pem.key';

    private $privateKey;

    private $apiUrl;

    private $apiKey;

    private $cobrandName;

    private $username;

    public function __construct(array $client)
    {
        $this->cobrandName = $client['cobrand_name'];
        $this->apiUrl = $client['api_url'];
        $this->apiKey = $client['api_key'];
        $this->username = $client['username'];

        $cwd = str_replace('/public', '', getcwd());

        $this->privateKey = file_get_contents(
            $cwd
                .$this->privateKeyStoragePath
                .$this->privateKeyFilename
        );
    }

    public function apiGet($endpoint, $username = null)
    {
        if ($username == null) {
            $token = $this->generateJwtToken($this->username);
            ray('Defaulting to stored username')->orange();
        } else {
            $token = $this->generateJwtToken($username);
        }

        $api = new Api();

        $response = $api->get(
            $this->apiUrl.$endpoint,
            [
                'Api-Version: 1.1',
                'Authorization: Bearer '.$token,
                'Cobrand-Name: '.$this->cobrandName,
                'Content-Type: application/json',
            ]
        );

        return $response;
    }

    /**
     * Get all accounts of a user held at the providers, i.e. all their bank accounts.
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/Accounts/operation/getAllAccounts
     */
    public function getAccounts($user = null)
    {
        return $this->apiGet('accounts', $user);
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
        return $this->apiGet('auth/apiKey');
    }

    /**
     * Get a list of all the providers (financial institutions) that have been added by Yodlee based on a country code.
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/Providers/operation/getAllProviders
     */
    public function getProviders($priority = 'cobrand', $iso = 'ZA')
    {
        return $this->apiGet("providers?priority=$priority&countryISOCode=$iso");
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
        return $this->apiGet('providerAccounts', $username);
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

        return $this->apiGet($url, $username);
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

        return $this->apiGet($url, $username);
    }

    /**
     * The get user details service is used to get the user profile information
     * and the application preferences set at the time of user registration.
     *
     * https://developer.yodlee.com/api-reference#tag/User/operation/getUser
     */
    public function getUser($username)
    {
        return $this->apiGet('user', $username);
    }

    /**
     * You can call get user without a username which returns the first user.
     */
    public function getAllUsers($username = null)
    {
        return $this->apiGet('user', $username);
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
              "loginName":"'.$loginName.'",              
              "email": "'.$email.'",
              "preferences": {                
                "currency": "'.$currency.'",
                "timeZone": "'.$timeZone.'",
                "dateFormat": "'.$dateFormat.'",
                "locale": "'.$locale.'"
              }              
            }
          }
        ';

        $url = $this->apiUrl.'user/register';

        $header = [
            'Api-Version: 1.1',
            'Cobrand-Name: '.$this->cobrandName,
            'Authorization: Bearer '.$this->generateGenericJwtToken(),
            'Content-Type: application/json',
        ];

        $api = new Api();

        $result = json_decode($api->post($url, $postFields, $header));

        if (isset($result->errorMessage)) {
            throw new Exception($result->errorMessage);
        }

        return $result;
    }

    /**
     * Delete a user (Yodlee consumer). This is also called unregister a user.
     *
     * On success this method returns an empty string but on the CURL request is generates a 204 (Success without content)
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/User/operation/userLogout
     */
    public function unregisterUser($loginName)
    {
        $url = $this->apiUrl.'user/unregister';

        $header = [
            'Api-Version: 1.1',
            'Authorization: Bearer '.$this->generateJwtToken($loginName),
            'Cobrand-Name: '.$this->cobrandName,
            'Content-Type: application/json',
        ];

        $api = new Api();

        $result = json_decode($api->delete($url, '', $header));

        if (isset($result->errorMessage)) {
            throw new Exception($result->errorMessage);
        }

        return $result;
    }

    /**
     * Deprecate, we refer to this now as unregisterUser to keep it consistent
     * of how it's named in the API.
     */
    public function deleteUser($loginName)
    {
        return $this->unregisterUser($loginName);
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
        		"publicKey": "'.$publicKey.'"
  			}',
            CURLOPT_HTTPHEADER => [
                'Api-Version: 1.1',
                'Authorization: cobSession='.$cobrandArray['cobSession'],
                'Cobrand-Name: '.$cobrandArray['cobrandName'],
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $object = json_decode($response);

        // If a key doesn't exist after the API call, then just return the response which should contain the error
        return $object->apiKey['0']->key ?? $response;
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

    /**
     * Duplicate of generateGenericJwtToken, TODO consolidate.
     */
    public function generateJwtToken($username)
    {
        $payload = [
            'iss' => $this->apiKey,
            'iat' => time(),
            'exp' => time() + 1800,
            'sub' => $username,
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
					"cobrandLogin": "'.$cobrandArray['cobrandLogin'].'",
					"cobrandPassword": "'.$cobrandArray['cobrandPassword'].'"
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
