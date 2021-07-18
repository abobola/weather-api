<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Application\Query;
use App\Shared\Application\QueryBus as QueryBusInterface;
use App\Shared\Application\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function ask(Query $query): Response
    {
        return $this->handle($query);
    }
}
