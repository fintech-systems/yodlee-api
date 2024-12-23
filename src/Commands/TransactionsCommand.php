<?php

namespace FintechSystems\YodleeApi\Commands;

use FintechSystems\LaravelApiHelpers\Commands\LaravelApiHelpersCommand;
use FintechSystems\YodleeApi\Facades\YodleeApi;
use Illuminate\Console\Command;

class TransactionsCommand extends LaravelApiHelpersCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yodlee:transactions {username} {--cached}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch a list of Yodlee transactions for a user';

    public $cachedFile = 'transactions.cache.json';

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
     * @return int
     */
    public function handle()
    {
        if ($file = $this->checkCachedFileExists()) {
            $this->info('A cached file was returned');

            return $file;
        }

        $transactions = YodleeApi::getTransactions($this->argument('username'));

        ray($transactions->json());

        $result = file_put_contents($this->cachedFile, $transactions);

        $this->info('The API command was successful');

        return $result;
    }
}
