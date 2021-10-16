<?php

namespace FintechSystems\YodleeApi\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use FintechSystems\YodleeApi\Facades\YodleeApi;

class RegisterUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yodlee:register-user {username} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register a new Yodlee user';

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
        $result = YodleeApi::registerUser(
            $this->argument('username'),
            $this->argument('email')
        );

        if (isset($result->errorCode)) {
            $this->error($result->errorMessage); // Output the error to the console

            Log::error($result->errorMessage);

            return -1;
        }

        $this->info($result->user->id);

        return $result->user->id;
    }
}
