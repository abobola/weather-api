<?php

declare(strict_types=1);

namespace App\Forecast\Application;

use App\Forecast\Domain\Location;
use App\Shared\Application\QueryBus;

class TextForecastProvider
{
    private const DAYS_SEPARATOR = ' - ';

    public function __construct(private QueryBus $queryBus)
    {
    }

    public function __invoke(Location $location, int $days): string
    {
        /** @var FindForecastResponse $result */
        $result = $this->queryBus->ask(
            new FindForecastQuery($location->getLatitude(), $location->getLongitude(), $days)
        );

        $forecasts = [];
        foreach ($result->getForecasts() as $forecast) {
            $forecasts[$forecast->getDay()] = (string) $forecast->getCondition();
        }

        ksort($forecasts);

        return implode(self::DAYS_SEPARATOR, $forecasts);
    }
}
