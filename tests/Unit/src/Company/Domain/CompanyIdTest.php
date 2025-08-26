<?php

declare(strict_types=1);

namespace Tests\Unit\Company\Domain;

use App\Company\Domain\CompanyId;
use PHPUnit\Framework\TestCase;

final class CompanyIdTest extends TestCase
{
    public function testValidConstruction(): void
    {
        $id = new CompanyId(123);
        
        $this->assertEquals(123, $id->value);
    }

    public function testInvalidConstructionWithZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You must provide a positive integer');
        
        new CompanyId(0);
    }

    public function testInvalidConstructionWithNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You must provide a positive integer');
        
        new CompanyId(-5);
    }
}