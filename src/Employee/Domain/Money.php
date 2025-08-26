<?php

declare(strict_types=1);

namespace App\Employee\Domain;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(public float $amount)
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative');
        }
    }

    public function equals(Money $other): bool
    {
        return abs($this->amount - $other->amount) < 0.01;
    }
}
