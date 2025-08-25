<?php

declare(strict_types=1);

namespace Tests\Unit\Employee\Domain;

use App\Employee\Domain\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testValidConstruction(): void
    {
        $money = new Money(1000.50);
        
        $this->assertEquals(1000.50, $money->amount);
    }

    public function testZeroAmount(): void
    {
        $money = new Money(0.0);
        
        $this->assertEquals(0.0, $money->amount);
    }

    public function testInvalidConstructionWithNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Money amount cannot be negative');
        
        new Money(-100.0);
    }

    public function testEquals(): void
    {
        $money = new Money(1000.50);

        $this->assertTrue($money->equals(new Money(1000.50)));
    }
}
