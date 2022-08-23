<?php

namespace FintechSystems\YodleeApi\Enums;

class SubscriptionNotificationEvent
{
    const REFRESH = 'REFRESH';
    const DATA_UPDATES = 'DATA_UPDATES';
    const AUTO_REFRESH_UPDATES = 'AUTO_REFRESH_UPDATES';
    const LATEST_BALANCE_UPDATES = 'LATEST_BALANCE_UPDATES';

    public static function options()
    {
        return [
            self::REFRESH => 'Refresh',
            self::DATA_UPDATES => 'Data Updates',
            self::AUTO_REFRESH_UPDATES => 'Auto Refresh Updates',
            self::LATEST_BALANCE_UPDATES => 'Latest Balance Updates',
            
        ];
    }
}
