<?php

namespace Tests\Unit\Core\Domain;

use App\Core\Domain\PositiveInt;
use PHPUnit\Framework\TestCase;

final class PositiveIntTest extends TestCase
{
    public function testValidConstruction(): void
    {
        $id = new PositiveInt(42);

        $this->assertEquals(42, $id->value);
    }

    public function testInvalidConstructionWithZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You must provide a positive integer');

        new PositiveInt(0);
    }

    public function testInvalidConstructionWithNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You must provide a positive integer');

        new PositiveInt(-1);
    }

    public function testGetValue(): void
    {
        $id = new PositiveInt(42);

        $this->assertEquals(42, $id->value());
    }

    public function testEquals(): void
    {
        $id = new PositiveInt(42);

        $this->assertTrue($id->equals(new PositiveInt(42)));
    }

    public function testNotEquals(): void
    {
        $id = new PositiveInt(42);

        $this->assertFalse($id->equals(new PositiveInt(43)));
    }
}
