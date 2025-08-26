<?php

declare(strict_types=1);

namespace Tests\Unit\Employee\Domain;

use App\Employee\Domain\EmployeeId;
use PHPUnit\Framework\TestCase;

final class EmployeeIdTest extends TestCase
{
    public function testValidConstruction(): void
    {
        $id = new EmployeeId(42);
        
        $this->assertEquals(42, $id->value);
    }
}
