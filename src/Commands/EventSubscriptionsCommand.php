<?php

namespace FintechSystems\YodleeApi\Commands;

use FintechSystems\YodleeApi\Facades\YodleeApi;
use Illuminate\Console\Command;

class EventSubscriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yodlee:event-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch a list of subscribed notification events';

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
        $response = YodleeApi::getSubscribedNotificationEvents();

        $event = $response->json()['event'];

        ray($event);

        return $event;
    }
}
