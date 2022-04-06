<?php

declare(strict_types=1);

namespace App\Forecast\Domain;

final class Location implements \Stringable
{
    public function __construct(private string $city, private float $latitude, private float $longitude)
    {
    }

    public function __toString(): string
    {
        return $this->getCity();
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
