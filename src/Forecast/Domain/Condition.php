<?php

declare(strict_types=1);

namespace App\Forecast\Domain;

final class Condition implements \Stringable
{
    public function __construct(private string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->value;
    }
}
