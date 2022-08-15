<?php

declare(strict_types=1);

namespace App\Forecast\Domain;

final class Forecast
{
    public function __construct(private int $day, private Condition $condition)
    {
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function getCondition(): Condition
    {
        return $this->condition;
    }
}
