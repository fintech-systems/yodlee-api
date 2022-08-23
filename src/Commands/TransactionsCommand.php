<?php

namespace FintechSystems\YodleeApi\Commands;

use FintechSystems\LaravelApiHelpers\Commands\ApiHelpersCommand;
use FintechSystems\YodleeApi\Facades\YodleeApi;
use Illuminate\Console\Command;

class TransactionsCommand extends ApiHelpersCommand
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
    protected $description = 'Retrieve a list of Yodlee transactions for a user';

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

        $result = file_put_contents($this->cachedFile, YodleeApi::getTransactions(
            $this->argument('username')
        ));

        $this->info('The API command was successful');

        return $result;
    }
}
