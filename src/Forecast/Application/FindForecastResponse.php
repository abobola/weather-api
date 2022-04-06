<?php

declare(strict_types=1);

namespace App\Forecast\Application;

use App\Forecast\Domain\Forecast;
use App\Shared\Application\Response;

final class FindForecastResponse implements Response
{
    /** @var Forecast[] */
    private array $forecasts = [];

    /**
     * @param iterable<Forecast> $forecasts
     */
    public function __construct(iterable $forecasts)
    {
        foreach ($forecasts as $forecast) {
            $this->addForecast($forecast);
        }
    }

    /**
     * @return Forecast[]
     */
    public function getForecasts(): array
    {
        return $this->forecasts;
    }

    private function addForecast(Forecast $forecast): void
    {
        $this->forecasts[] = $forecast;
    }
}
