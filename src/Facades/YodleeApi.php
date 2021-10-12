<?php

namespace FintechSystems\YodleeApi\Facades;

use Illuminate\Support\Facades\Facade;

class YodleeApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'yodlee-api';
    }
}
