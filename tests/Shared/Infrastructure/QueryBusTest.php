<?php

declare(strict_types=1);

namespace App\Tests\Shared\Infrastructure;

use App\Shared\Application\Query;
use App\Shared\Application\Response;
use App\Shared\Infrastructure\QueryBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class QueryBusTest extends TestCase
{
    /** @var MessageBusInterface&MockObject */
    private MessageBusInterface | MockObject $messageBus;

    private QueryBus $queryBus;

    public function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->queryBus = new QueryBus($this->messageBus);
    }

    public function testAsk(): void
    {
        $query = $this->createMock(Query::class);
        $response = $this->createMock(Response::class);

        $stamp = new HandledStamp($response, 'QueryHandler');
        $envelope = new Envelope($query, [$stamp]);

        $this->messageBus->method('dispatch')
            ->with($query)
            ->willReturn($envelope);

        $this->assertSame($response, $this->queryBus->ask($query));
    }
}
