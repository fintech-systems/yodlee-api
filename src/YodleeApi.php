<?php

namespace FintechSystems\YodleeApi;

use App\Models\Account;
use Carbon\Carbon;
use Facades\App\Services\AccountService;
use FintechSystems\YodleeApi\Contracts\BankingProvider;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class YodleeApi implements BankingProvider
{
    public $storagePath = '/yodlee/';

    private $privateKeyFilename = 'private.pem';

    private $api_url;

    public function __construct(array $client)
    {
        $this->api_url = $client['api_url'];
        $this->api_key = $client['api_key'];
    }

    public function apiGet($endpoint)
    {
        ray('Yodlee apiGet endpoint: ' . $this->api_url . $endpoint);

        $token = $this->generateJwtToken();

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->api_url . '/' . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Api-Version: 1.1',
                'Authorization: Bearer ' . $token,
                'Cobrand-Name: xxx', // REDACTED
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);

        ray(json_decode($response));

        curl_close($curl);

        return $response;
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
     * Generate a JWT token from the private key. Used in most requests.
     */
    public function generateJwtToken()
    {
        $api_key = $_ENV['YODLEE_API_KEY'];

        $username = $_ENV['YODLEE_USERNAME'];

        $privateKey = file_get_contents(__DIR__ . '/../' . $this->privateKeyFilename);

        $payload = [
            'iss' => $api_key,
            'iat' => time(),
            'exp' => time() + 1800,
            'sub' => $username,
        ];

        return JWT::encode($payload, $privateKey, 'RS512');
    }

    /**
     * Original getAccounts() method that include json decoding.
     */
    public function getAccounts()
    {
        return json_decode($this->apiGet('accounts'));
    }

    /**
     * New GetAccounts API that retrieves accounts without json encode.
     */
    public function getAccounts2()
    {
        return $this->apiGet('accounts');
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

    private function getTransactions()
    {
        return json_decode($this->apiGet('transactions?fromDate=2020-08-01'));
    }

    private function getTransactionsByAccount($accountId, $fromDate = null)
    {
        $fromDate == null
            ? $fromDate = Carbon::now()->subDays(90)->format('Y-m-d')
            : $fromDate = $fromDate;

        return json_decode($this->apiGet("transactions?account_id=$accountId&fromDate=$fromDate"));
    }

    /**
     * Import accounts from Yodlee by reading the local accounts.json file outputted to disk
     * Only update fields directly from the Yodlee source, which should
     * exclude the user's assigned name and nickname.
     */
    public function importAccounts()
    {
        $json = json_decode(Storage::disk('local')->get($this->storagePath . 'accounts.json'));

        $accounts = $json->account;

        foreach ($accounts as $account) {
            Account::updateOrCreate( // TODO Abstract
                [
                    'yodlee_account_id' => $account->id,
                ],
                [
                    'user_id'             => 3,
                    'container'           => $account->CONTAINER,
                    'provider_account_id' => $account->providerAccountId,
                    'name'                => $account->accountName,
                    'number'              => $account->accountNumber,
                    'balance'             => $account->balance->amount,
                    'available_balance'   => $account->availableBalance->amount ?? null,
                    'current_balance'     => $account->currentBalance->amount ?? null,
                    'currency'            => $account->balance->currency,
                    'provider_id'         => $account->providerId,
                    'provider_name'       => $account->providerName,
                    'type'                => $account->accountType,
                    'display_name'        => $account->displayedName ?? null,
                    'classification'      => $account->classification ?? null,
                    'interest_rate'       => $account->interestRateType ?? null,
                    'yodlee_dataset_name' => $account->dataset[0]->name,
                    'yodlee_updated_at'   => Carbon::parse($account->dataset[0]->lastUpdated)->format('Y-m-d H:i:s'),
                ]
            );

            $message = "Imported $account->CONTAINER account $account->accountName #$account->accountNumber with account balance of {$account->balance->amount} from $account->providerName for tentant 3\n";

            Log::info($message);
            echo $message;
            ray($message)->green();
        }
    }

    public function importTransactions($file = null)
    {
        $file == null ? $file = 'transactions.json' : $file = $file;

        $json = json_decode(Storage::disk('local')->get($this->storagePath . $file));

        $transactions = $json->transaction;

        AccountService::import($transactions, 3);    // TODO Abstract
    }

    public function refreshAccounts()
    {
        $accounts = $this->getAccounts();

        Storage::put($this->storagePath . 'accounts.json', json_encode($accounts));

        $message = 'Retrieved ' . count($accounts->account) . ' accounts';

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
        $userJwtToken = $this->generateJWTToken();

        $transactions = $this->getTransactionsByAccount($userJwtToken, $accountId);

        Storage::put("$this->storagePath$accountId.json", json_encode($transactions));

        $message = 'Retrieved ' . count($transactions->transaction) . ' transactions';

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

        Storage::put($this->storagePath . 'transactions.json', json_encode($transactions));

        $message = 'Retrieved ' . count($transactions->transaction) . ' transactions';

        Log::info($message);

        echo $message;

        ray($message)->green();
    }

    public function registerUser()
    {
        $rawData = '
        {
            "user": {
                "loginName": "jan.smit",
                "email": "jsmit@voorbeeld.com",
                "name": {
                    "first": "Jan",
                    "last": "Smit"
                },
                "address": {
                    "address1": "20 Kloof Street",
                    "state": "Western Cape",
                    "city": "Cape Town",
                    "zip": "8001",
                    "country": "South Africa"
                },
                "preferences": {
                    "currency": "ZAR",
                    "timeZone": "GMT+2",
                    "dateFormat": "dd/MM/yyyy",
                    "locale": "en_ZA"
                }
            }
        }';
        $endPoint = $this->api_url . 'user/register';
        
    }

    public function user()
    {
        return $this->apiGet('user');
    }
}
