<?php

namespace FintechSystems\YodleeApi\Tests;

use PHPUnit\Framework\TestCase;

class Setup extends TestCase
{
    /**
     * Read and store the environment.
     */
    protected function init()
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();
    }

    /**
     * Read the Cobrand and API credentials from the environment.
     */
    protected function getClient()
    {
        $this->init();
        
        return [
            'cobrand_name'     => $_ENV['YODLEE_COBRAND_NAME'],
            'cobrand_login'    => $_ENV['YODLEE_COBRAND_LOGIN'],
            'cobrand_password' => $_ENV['YODLEE_COBRAND_PASSWORD'],
            'api_url'          => $_ENV['YODLEE_API_URL'],
            'api_key'          => $_ENV['YODLEE_API_KEY'],
            'username'         => $_ENV['YODLEE_USERNAME'],            
        ];
    }
}
