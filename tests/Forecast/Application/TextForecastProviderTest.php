<?php

declare(strict_types=1);

namespace App\Tests\Forecast\Application;

use App\Forecast\Application\FindForecastQuery;
use App\Forecast\Application\FindForecastResponse;
use App\Forecast\Application\TextForecastProvider;
use App\Forecast\Domain\Condition;
use App\Forecast\Domain\Forecast;
use App\Forecast\Domain\Location;
use App\Shared\Application\QueryBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TextForecastProviderTest extends TestCase
{
    /** @var QueryBus&MockObject */
    private QueryBus | MockObject $queryBus;

    private TextForecastProvider $provider;

    public function setUp(): void
    {
        $this->queryBus = $this->createMock(QueryBus::class);
        $this->provider = new TextForecastProvider($this->queryBus);
    }

    public function testProvide(): void
    {
        $location = new Location('Casa Pertud', 45.84, 6.86);
        $days = 2;

        $this->queryBus->method('ask')
            ->with(new FindForecastQuery($location->getLatitude(), $location->getLongitude(), $days))
            ->willReturn(new FindForecastResponse([
                new Forecast(0, new Condition('Patchy rain possible')),
                new Forecast(1, new Condition('Patchy rain possible')),
            ]));

        $this->assertSame(
            'Patchy rain possible - Patchy rain possible',
            $this->provider->__invoke($location, $days)
        );
    }
}
