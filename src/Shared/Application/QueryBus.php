<?php

namespace App\Shared\Application;

interface QueryBus
{
    public function ask(Query $query): ?Response;
}
