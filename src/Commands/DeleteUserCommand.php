<?php

namespace FintechSystems\YodleeApi\Commands;

use FintechSystems\YodleeApi\Facades\YodleeApi;
use Illuminate\Console\Command;

class DeleteUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yodlee:delete-user {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an existing Yodlee user';

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
     * Execute the console command. Return
     * 0 if the result was a HTTP 204
     * code, otherwise return 1.
     *
     * @return int
     */
    public function handle()
    {
        $response = YodleeApi::unregisterUser(
            $this->argument('username')
        );

        if ($response->status() == 204) {
            return 0;
        }

        // https://shapeshed.com/unix-exit-codes/
        return 1;
    }
}
