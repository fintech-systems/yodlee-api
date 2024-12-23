<?php

namespace FintechSystems\YodleeApi\Commands;

use FintechSystems\YodleeApi\Facades\YodleeApi;
use Illuminate\Console\Command;

class ProviderAccountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yodlee:provider-accounts {username} {--cached}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch a list of Yodlee provider accounts';

    private $cachedFile = 'provider-accounts.cache.json';

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
        if ($this->option('cached')) {
            if (! file_exists($this->cachedFile)) {
                $this->error("--cached was specified but the file $this->cachedFile does not exist");

                return;
            }

            return file_get_contents($this->cachedFile);
        }

        $providerAccounts = YodleeApi::getProviderAccounts($this->argument('username'));

        ray($providerAccounts->json());

        $result = file_put_contents($this->cachedFile, YodleeApi::getProviderAccounts(
            $this->argument('username')
        ));
        $this->info('The command was successful!');

        return $result;
    }
}
