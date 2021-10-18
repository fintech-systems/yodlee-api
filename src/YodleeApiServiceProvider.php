<?php

namespace FintechSystems\YodleeApi;

use FintechSystems\YodleeApi\Commands\AccountsCommand;
use FintechSystems\YodleeApi\Commands\ApiKeyCommand;
use FintechSystems\YodleeApi\Commands\DeleteUserCommand;
use FintechSystems\YodleeApi\Commands\GetUserCommand;
use FintechSystems\YodleeApi\Commands\ProviderAccountsCommand;
use FintechSystems\YodleeApi\Commands\ProvidersCommand;
use FintechSystems\YodleeApi\Commands\RegisterUserCommand;
use FintechSystems\YodleeApi\Commands\TransactionsCommand;
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
                AccountsCommand::class,
                ApiKeyCommand::class,
                DeleteUserCommand::class,
                GetUserCommand::class,
                ProvidersCommand::class,
                ProviderAccountsCommand::class,
                RegisterUserCommand::class,
                TransactionsCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->app->bind('yodlee-api', function () {
            return new YodleeApi([
                'cobrand_name'     => config('yodlee.cobrand_name'),
                'cobrand_login'    => config('yodlee.cobrand_login'),
                'cobrand_password' => config('yodlee.cobrand_password'),
                'api_url'          => config('yodlee.api_url'),
                'api_key'          => config('yodlee.api_key'),
                'username'         => config('yodlee.username'),
            ]);
        });
    }
}
