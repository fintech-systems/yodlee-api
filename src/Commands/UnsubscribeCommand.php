<?php

namespace FintechSystems\YodleeApi\Commands;

use Exception;
use Illuminate\Console\Command;
use FintechSystems\YodleeApi\Facades\YodleeApi;
use FintechSystems\YodleeApi\Enums\SubscriptionNotificationEvent;

class UnsubscribeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yodlee:unsubscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unsubscribe from DATA_UPDATES event notifications';

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
        $response = YodleeApi::deleteNotificationSubscription(
            SubscriptionNotificationEvent::DATA_UPDATES
        );

        if ($response->getStatusCode() != 204) {            
            throw new Exception($response->json()['errorMessage']);
        }
    }
}
