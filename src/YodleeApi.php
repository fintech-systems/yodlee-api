<?php

namespace FintechSystems\YodleeApi;

use App\Models\Account;
use Carbon\Carbon;
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
                . $this->privateKeyStoragePath
                . $this->privateKeyFilename
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

        $api = new Api;

        $response = $api->get(
            $this->apiUrl . $endpoint,
            [
                'Api-Version: 1.1',
                'Authorization: Bearer ' . $token,
                'Cobrand-Name: ' . $this->cobrandName,
                'Content-Type: application/json',
            ]
        );

        ray(json_decode($response));

        return $response;
    }

    /**
     * Delete a user (Yodlee consumer).
     *
     * On success this method returns an empty string but on the CURL request is generates a 204 (Success without content)
     *
     * https://developer.yodlee.com/api-reference/aggregation#operation/userLogout
     */
    public function deleteUser($loginName)
    {
        $url = $this->apiUrl . 'user/unregister';

        $header = [
            'Api-Version: 1.1',
            'Authorization: Bearer ' . $this->generateJwtToken($loginName),
            'Cobrand-Name: ' . $this->cobrandName,
            'Content-Type: application/json',
        ];

        $api = new Api;

        $result = $api->delete($url, $header);

        return $result;
    }

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

        // If a key doesn't exist after the API call, then just return the response which should contain the error
        return $object->apiKey['0']->key ?? $response;
    }

    /**
     * Generic tokens are used for global API calls that does not pertain to a user.
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

    /**
     * Get all accounts of a user held at the providers, i.e. all their bank accounts.
     */
    public function getAccounts($user = null)
    {
        return $this->apiGet('accounts', $user);
    }

    public function getApiKeys()
    {
        return $this->apiGet('auth/apiKey');
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
                'Cobrand-Name: ' . $cobrandArray['cobrandName'],
                'Content-Type: application/json',
                'Cookie: JSESSIONID=xxx', // REDACTED TODO Research
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $object = json_decode($response);

        ray($object);

        return $object->session->cobSession;
    }

    /**
     * Get a list of provider accounts added by the user.
     *
     * This includes the failed and successfully added provider accounts.
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/ProviderAccounts
     */
    public function getProviderAccounts($username)
    {
        return $this->apiGet('providerAccounts', $username);
    }

    /**
     * Get a list of every all the providers that have been added by Yodlee.
     *
     * https://developer.yodlee.com/api-reference/aggregation#tag/Providers
     */
    public function getProviders($priority = 'cobrand')
    {
        return $this->apiGet("providers?priority=$priority&countryISOCode=ZA");
    }

    /**
     * Get all transactions for the last 30 days.
     *
     * https://developer.yodlee.com/api-reference/aggregation#operation/getTransactions
     */
    public function getTransactions($username, $fromDate = null)
    {
        $fromDate == null
            ? $fromDate = Carbon::now()->subDays(30)->format('Y-m-d')
            : $fromDate = $fromDate;

        $url = "transactions?fromDate=$fromDate";

        return $this->apiGet($url, $username);
    }

    /**
     * Get transactions for the last 90 days for a specific account only.
     *
     * https://developer.yodlee.com/api-reference/aggregation#operation/getTransactions
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
     * Add a new user (consumer) to the system.
     *
     * https://developer.yodlee.com/api-reference/aggregation#operation/registerUser
     */
    public function registerUser($loginName, $email)
    {
        $postFields = '
        {
            "user": {
              "loginName":"' . $loginName . '",              
              "email": "' . $email . '",
              "preferences": {                
                "currency": "ZAR",
                "timeZone": "GMT+2",
                "dateFormat": "yyyy-MM-dd",
                "locale": "en_ZA"
              }              
            }
          }
        ';

        $url = $this->apiUrl . 'user/register';

        $header = [
            'Api-Version: 1.1',
            'Cobrand-Name: ' . $this->cobrandName,
            'Authorization: Bearer ' . $this->generateGenericJwtToken(),
            'Content-Type: application/json',
        ];

        $api = new Api;

        $result = $api->post($url, $postFields, $header);

        ray(json_decode($result));

        return json_decode($result);
    }

    /**
     * getUser.
     *
     * https://developer.yodlee.com/api-reference#operation/getUser
     */
    public function getUser($username)
    {
        return $this->apiGet('user', $username);
    }
}
