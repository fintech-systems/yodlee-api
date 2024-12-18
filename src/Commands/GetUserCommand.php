<?php

namespace FintechSystems\YodleeApi\Commands;

use FintechSystems\LaravelApiHelpers\Commands\LaravelApiHelpersCommand;
use FintechSystems\YodleeApi\Facades\YodleeApi;

class GetUserCommand extends LaravelApiHelpersCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yodlee:get-user {username} {--cached}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch details about a Yodlee user';

    public $cachedFile = 'user.cache.json';

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

        $this->info('Fetching user details from Yodlee');

        $user = YodleeApi::getUser($this->argument('username'));
        ray($user->json());

        $result = file_put_contents($this->cachedFile, YodleeApi::getUser(
            $this->argument('username')
        ));

        $this->info("Got " . $user->json()['user']['email']);

        return $result;
    }
}
