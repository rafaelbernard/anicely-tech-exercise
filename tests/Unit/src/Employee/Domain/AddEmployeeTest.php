<?php

declare(strict_types=1);

namespace Tests\Unit\Employee\Domain;

use App\Company\Domain\CompanyId;
use App\Core\Domain\Email;
use App\Employee\Domain\AddEmployee;
use App\Employee\Domain\Money;
use PHPUnit\Framework\TestCase;

final class AddEmployeeTest extends TestCase
{
    public function testConstruction(): void
    {
        $companyId = new CompanyId(1);
        $email = new Email('test@example.com');
        $salary = new Money(50000.0);
        
        $command = new AddEmployee($companyId, 'John Doe', $email, $salary);
        
        $this->assertSame($companyId, $command->companyId);
        $this->assertEquals('John Doe', $command->employeeName);
        $this->assertSame($email, $command->email);
        $this->assertSame($salary, $command->salary);
    }
}