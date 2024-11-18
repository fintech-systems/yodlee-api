<?php

namespace FintechSystems\YodleeApi\Commands;

use FintechSystems\LaravelApiHelpers\Commands\LaravelApiHelpersCommand;
use FintechSystems\YodleeApi\Facades\YodleeApi;
use Illuminate\Console\Command;

class AccountsCommand extends LaravelApiHelpersCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yodlee:accounts {username : The ID of the user} {--cached : Whetever a cached file should be retrieved}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch a list of Yodlee accounts';

    public $cachedFile = 'accounts.cache.json';

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

        $result = file_put_contents($this->cachedFile, YodleeApi::getAccounts(
            $this->argument('username')
        ));

        ray($result);

        $this->info('The API command was successful');

        return $result;
    }
}
