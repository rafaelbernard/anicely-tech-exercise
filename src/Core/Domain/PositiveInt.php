<?php

declare(strict_types=1);

namespace App\Core\Domain;

use InvalidArgumentException;

readonly class PositiveInt
{
    public function __construct(public int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('You must provide a positive integer');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
