<?php

namespace FintechSystems\YodleeApi;

use App\Models\Account;
use Carbon\Carbon;
use FintechSystems\LaravelApiHelpers\Api;
use FintechSystems\YodleeApi\Contracts\BankingProvider;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class YodleeApi implements BankingProvider
{
    public $storagePath = '/yodlee/';

    private $privateKeyFilename = 'private.pem';

    private $privateKey;

    private $apiUrl;

    private $apiKey;

    private $cobrandName;

    private $username;

    public function __construct(array $client)
    {
        $this->apiUrl = $client['api_url'];
        $this->apiKey = $client['api_key'];
        $this->cobrandName = $client['cobrand_name'];
        $this->username = $client['username'];
        $this->privateKey = file_get_contents(__DIR__.'/../'.$this->privateKeyFilename);
    }

    public function apiGet($endpoint, $username = null)
    {
        if ($username == null) {
            Log::warning("No username was specified so generating a token for $this->username");
            $token = $this->generateJwtToken($this->username);
        } else {
            $token = $this->generateJwtToken($username);
        }

        $api = new Api;

        $response = $api->get(
            $this->apiUrl.$endpoint,
            [
                'Api-Version: 1.1',
                'Authorization: Bearer '.$token,
                'Cobrand-Name: xxx', // REDACTED
                'Content-Type: application/json',
            ]
        );

        ray(json_decode($response));

        return $response;
    }

    public function deleteUser($loginName)
    {
        $url = $this->apiUrl.'user/unregister';

        $header = [
            'Api-Version: 1.1',
            'Cobrand-Name: '.$this->cobrandName,
            'Authorization: Bearer '.$this->generateJwtToken($loginName),
            'Content-Type: application/json',
        ];

        $api = new Api;

        $result = $api->delete($url, $header);

        ray('Outputting the result of a Yodlee delete command');

        ray($result);

        ray(json_decode($result));

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
     * Original getAccounts() method that includes json decoding.
     */
    public function getAccounts()
    {
        return json_decode($this->apiGet('accounts'));
    }

    /**
     * New GetAccounts API that retrieves accounts without json encoding.
     */
    public function getAccounts2($loginName)
    {
        return $this->apiGet('accounts', $loginName);
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
					"cobrandLogin": "'.$cobrandArray['cobrandLogin'].'",
					"cobrandPassword": "'.$cobrandArray['cobrandPassword'].'"
         		}
    		}',
            CURLOPT_HTTPHEADER => [
                'Api-Version: 1.1',
                'Cobrand-Name: '.$cobrandArray['cobrandName'],
                'Content-Type: application/json',
                'Cookie: JSESSIONID=xxx', // REDACTED TO Research
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $object = json_decode($response);

        ray($object);

        return $object->session->cobSession;
    }

    public function getProviderAccounts()
    {
        return $this->apiGet('providerAccounts');
    }

    /**
     * Get a list of providers.
     */
    public function getProviders($priority = 'cobrand')
    {
        return $this->apiGet("providers?priority=$priority&countryISOCode=ZA");
    }

    /**
     * TODO Deprecate, reasons:.
     *
     * 1. json decoding happening too quickly
     * 2. fromDate hardcoded
     */
    private function getTransactions()
    {
        return json_decode($this->apiGet('transactions?fromDate=2020-08-01'));
    }

    public function getTransactions2($loginName)
    {
        // return $this->apiGet('transactions?fromDate=2021-10-01', $loginName);
        return $this->apiGet('transactions', $loginName);
    }

    private function getTransactionsByAccount($accountId, $fromDate = null)
    {
        $fromDate == null
            ? $fromDate = Carbon::now()->subDays(90)->format('Y-m-d')
            : $fromDate = $fromDate;

        return json_decode($this->apiGet("transactions?account_id=$accountId&fromDate=$fromDate"));
    }

    public function refreshAccounts()
    {
        $accounts = $this->getAccounts();

        Storage::put($this->storagePath.'accounts.json', json_encode($accounts));

        $message = 'Retrieved '.count($accounts->account).' accounts';

        Log::info($message);

        echo $message;

        ray($message)->green();
    }

    /**
     * Calls the Yodlee API and retrieves transactions for a specific account up to
     * 90 days prior. The resultant output is stored on disk where it will
     * typically be processed by an import command.
     */
    public function refreshTransactionsByAccount($accountId)
    {
        $userJwtToken = $this->generateJWTToken($this->username);

        $transactions = $this->getTransactionsByAccount($userJwtToken, $accountId);

        Storage::put("$this->storagePath$accountId.json", json_encode($transactions));

        $message = 'Retrieved '.count($transactions->transaction).' transactions';

        Log::info($message);

        echo $message;

        ray($message)->green();
    }

    public function refreshTransactions($fromDate = null)
    {
        $fromDate == null
            ? $fromDate = Carbon::now()->subDays(90)->format('Y-m-d')
            : $fromDate = $fromDate;

        $transactions = $this->getTransactions($fromDate);

        Storage::put($this->storagePath.'transactions.json', json_encode($transactions));

        $message = 'Retrieved '.count($transactions->transaction).' transactions';

        Log::info($message);

        echo $message;

        ray($message)->green();
    }

    public function registerUser($loginName, $email)
    {
        $postFields = '
        {
            "user": {
              "loginName":"'.$loginName.'",              
              "email": "'.$email.'",
              "preferences": {                
                "currency": "ZAR",
                "timeZone": "GMT+2",
                "dateFormat": "yyyy-MM-dd",
                "locale": "en_ZA"
              }              
            }
          }
        ';

        ray($postFields);

        $url = $this->apiUrl.'user/register';

        $header = [
            'Api-Version: 1.1',
            'Cobrand-Name: '.$this->cobrandName,
            'Authorization: Bearer '.$this->generateGenericJwtToken(),
            'Content-Type: application/json',
        ];

        $api = new Api;

        $result = $api->post($url, $postFields, $header);

        ray(json_decode($result));

        return json_decode($result);
    }

    public function getUser($username)
    {
        ray('Trying to retrieve info for this user '.$username);

        return $this->apiGet('user', $username);
    }
}
