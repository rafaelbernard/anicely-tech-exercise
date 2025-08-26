<?php

declare(strict_types=1);

namespace Tests\Unit\Employee\Domain;

use App\Company\Domain\Company;
use App\Company\Domain\CompanyId;
use App\Core\Domain\Email;
use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeId;
use App\Employee\Domain\Money;
use PHPUnit\Framework\TestCase;

final class EmployeeTest extends TestCase
{
    public function testConstruction(): void
    {
        $id = new EmployeeId(1);
        $company = new Company(new CompanyId(1), 'ACME Corp');
        $email = new Email('john@acme.com');
        $salary = new Money(50000.0);
        
        $employee = new Employee($id, $company, 'John Doe', $email, $salary);
        
        $this->assertSame($id, $employee->id);
        $this->assertSame($company, $employee->company);
        $this->assertEquals('John Doe', $employee->employeeName);
        $this->assertSame($email, $employee->email);
        $this->assertSame($salary, $employee->salary);
    }

    public function testFromDbRow(): void
    {
        $row = [
            'id' => '1',
            'company_id' => '2',
            'company_name' => 'ACME Corp',
            'employee_name' => 'Jane Smith',
            'email_address' => 'jane@acme.com',
            'salary' => '75000.50'
        ];
        
        $employee = Employee::fromDbRow($row);
        
        $this->assertEquals(1, $employee->id->value);
        $this->assertEquals(2, $employee->company->id->value);
        $this->assertEquals('ACME Corp', $employee->company->name);
        $this->assertEquals('Jane Smith', $employee->employeeName);
        $this->assertEquals('jane@acme.com', $employee->email->value);
        $this->assertEquals(75000.50, $employee->salary->amount);
    }
}