<?php

namespace FintechSystems\YodleeApi\Commands;

use Illuminate\Console\Command;
use FintechSystems\YodleeApi\Facades\YodleeApi;

class ApiKeyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yodlee:api-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a list of Yodlee API keys';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * 
     * Inspired by https://laravelsecrets.com/
     *
     * @return int
     */
    public function handle()
    {
        $result = json_decode(YodleeApi::getApiKeys());        
        
        $result = collect($result->apiKey);    
            
        $headers = ['key', 'createdDate'];

        $data = $result->map(function ($key) {
            return [
                'key'         => $key->key,
                'createdDate' => $key->createdDate,
            ];
        });
        
        $this->table($headers, $data);                
    }
}
