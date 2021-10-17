<?php

namespace FintechSystems\YodleeApi\Contracts;

interface BankingProvider
{
    public function getAccounts(string $user);
}
