<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Application\Query;
use App\Shared\Application\QueryBus as QueryBusInterface;
use App\Shared\Application\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class QueryBus implements QueryBusInterface
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function ask(Query $query): ?Response
    {
        $envelope = $this->messageBus->dispatch($query);

        /** @var HandledStamp $stamp */
        $stamp = $envelope->last(HandledStamp::class);

        return $stamp->getResult();
    }
}
