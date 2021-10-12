<?php

namespace FintechSystems\YodleeApi;

use FintechSystems\YodleeApi\Commands\ApiKeyCommand;
use FintechSystems\YodleeApi\Commands\ProviderAccountsCommand;
use FintechSystems\YodleeApi\Commands\ProvidersCommand;
use Illuminate\Support\ServiceProvider;

class YodleeApiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/yodlee.php' => config_path('yodlee.php'),
        ], 'yodlee-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ApiKeyCommand::class,
                ProvidersCommand::class,
                ProviderAccountsCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->app->bind('yodlee-api', function () {
            return new YodleeApi([
                'cobrand_name'     => $_ENV['YODLEE_COBRAND_NAME'],
                'cobrand_login'    => $_ENV['YODLEE_COBRAND_LOGIN'],
                'cobrand_password' => $_ENV['YODLEE_COBRAND_PASSWORD'],
                'api_url'          => $_ENV['YODLEE_API_URL'],
                'api_key'          => $_ENV['YODLEE_API_KEY'],
                'username'         => $_ENV['YODLEE_USERNAME'],
            ]);
        });
    }
}
