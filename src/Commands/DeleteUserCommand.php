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
    protected $description = 'Delete a Yodlee user';    

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
        $result = YodleeApi::deleteUser($this->argument('username'));
        
        return $result;
    }
}
