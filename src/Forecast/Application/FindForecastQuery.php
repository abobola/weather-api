<?php

declare(strict_types=1);

namespace App\Forecast\Application;

use App\Shared\Application\Query;

final class FindForecastQuery implements Query
{
    public function __construct(private float $latitude, private float $longitude, private int $days = 1)
    {
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getDays(): int
    {
        return $this->days;
    }
}
