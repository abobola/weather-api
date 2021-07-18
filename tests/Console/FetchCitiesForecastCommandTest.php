<?php

declare(strict_types=1);

namespace App\Tests\Console;

use App\Console\FetchCitiesForecastCommand;
use App\Forecast\Application\FindCitiesQuery;
use App\Forecast\Application\FindCitiesResponse;
use App\Forecast\Application\TextForecastProvider;
use App\Forecast\Domain\Location;
use App\Shared\Application\QueryBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCitiesForecastCommandTest extends TestCase
{
    /** @var QueryBus&MockObject */
    private QueryBus | MockObject $queryBus;

    /** @var TextForecastProvider&MockObject */
    private TextForecastProvider | MockObject $forecastProvider;

    private FetchCitiesForecastCommand $command;

    public function setUp(): void
    {
        $this->queryBus = $this->createMock(QueryBus::class);
        $this->forecastProvider = $this->createMock(TextForecastProvider::class);

        $this->command = new FetchCitiesForecastCommand($this->queryBus, $this->forecastProvider);
    }

    public function testRun(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $cities = [
            new Location('Amsterdam', 52.374, 4.9),
            new Location('Paris', 48.866, 2.355),
        ];
        $days = 2;

        $this->queryBus->method('ask')
            ->with(new FindCitiesQuery())
            ->willReturn(new FindCitiesResponse($cities));

        $this->forecastProvider->method('__invoke')
            ->withConsecutive(
                [$cities[0], $days],
                [$cities[1], $days]
            )
            ->willReturnOnConsecutiveCalls(
                'Partly cloudy - Partly cloudy',
                'Sunny - Patchy rain possible'
            );

        $output->expects($this->exactly(2))
            ->method('writeln')
            ->withConsecutive(
                ['Processed city Amsterdam | Partly cloudy - Partly cloudy'],
                ['Processed city Paris | Sunny - Patchy rain possible']
            );

        $this->command->run($input, $output);
    }

    public function testConfiguration(): void
    {
        $this->assertSame(
            'app:fetch-cities-forecasts',
            $this->command->getName()
        );

        $this->assertSame(
            'Display a list of the cities with a forecast for the next 2 days',
            $this->command->getDescription()
        );
    }
}
