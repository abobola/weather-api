<?php

declare(strict_types=1);

namespace App\Forecast\Application;

use App\Forecast\Domain\Location;
use App\Shared\Application\Response;

final class FindCitiesResponse implements Response
{
    /** @var Location[] */
    private array $cities;

    /**
     * @param iterable<Location> $cities
     */
    public function __construct(iterable $cities)
    {
        foreach ($cities as $city) {
            $this->addCity($city);
        }
    }

    /**
     * @return Location[]
     */
    public function getCities(): array
    {
        return $this->cities;
    }

    private function addCity(Location $location): void
    {
        $this->cities[] = $location;
    }
}
