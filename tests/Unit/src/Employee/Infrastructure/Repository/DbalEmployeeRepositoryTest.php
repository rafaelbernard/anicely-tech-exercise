<?php

declare(strict_types=1);

namespace Tests\Unit\Employee\Infrastructure\Repository;

use App\Company\Domain\Company;
use App\Company\Domain\CompanyId;
use App\Core\Domain\Email;
use App\Employee\Domain\AddEmployee;
use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeId;
use App\Employee\Domain\Money;
use App\Employee\Infrastructure\Repository\DbalEmployeeRepository;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class DbalEmployeeRepositoryTest extends TestCase
{
    public function testUpdate(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalEmployeeRepository($connection);
        
        $employee = new Employee(
            new EmployeeId(1),
            new Company(new CompanyId(2), 'ACME Corp'),
            'John Doe',
            new Email('john@acme.com'),
            new Money(50000.0)
        );
        
        $connection->expects($this->once())
            ->method('update')
            ->with(
                'employees',
                [
                    'company_id' => 2,
                    'employee_name' => 'John Doe',
                    'email_address' => 'john@acme.com',
                    'salary' => 50000.0
                ],
                ['id' => 1]
            );
        
        $repository->update($employee);
    }

    public function testFindByIdReturnsEmployee(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalEmployeeRepository($connection);
        
        $row = [
            'id' => '1',
            'company_id' => '2',
            'company_name' => 'ACME Corp',
            'employee_name' => 'John Doe',
            'email_address' => 'john@acme.com',
            'salary' => '50000.0'
        ];
        
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->with(
                'SELECT e.*, c.name as company_name FROM employees e JOIN companies c ON e.company_id = c.id WHERE e.id = ?',
                [1]
            )
            ->willReturn($row);
        
        $employee = $repository->findById(new EmployeeId(1));
        
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals(1, $employee->id->value);
        $this->assertEquals('John Doe', $employee->employeeName);
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalEmployeeRepository($connection);
        
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->willReturn(false);
        
        $employee = $repository->findById(new EmployeeId(999));
        
        $this->assertNull($employee);
    }

    public function testDelete(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalEmployeeRepository($connection);
        
        $connection->expects($this->once())
            ->method('delete')
            ->with('employees', ['id' => 1]);
        
        $repository->delete(new EmployeeId(1));
    }

    public function testExistsByNameAndEmailReturnsTrue(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalEmployeeRepository($connection);
        
        $connection->expects($this->once())
            ->method('fetchOne')
            ->with(
                'SELECT COUNT(*) FROM employees WHERE employee_name = ? AND email_address = ?',
                ['John Doe', 'john@acme.com']
            )
            ->willReturn(1);
        
        $exists = $repository->existsByNameAndEmail('John Doe', 'john@acme.com');
        
        $this->assertTrue($exists);
    }

    public function testExistsByNameAndEmailReturnsFalse(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalEmployeeRepository($connection);
        
        $connection->expects($this->once())
            ->method('fetchOne')
            ->willReturn(0);
        
        $exists = $repository->existsByNameAndEmail('Jane Doe', 'jane@acme.com');
        
        $this->assertFalse($exists);
    }

    public function testAdd(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalEmployeeRepository($connection);
        
        $command = new AddEmployee(
            new CompanyId(1),
            'John Doe',
            new Email('john@acme.com'),
            new Money(50000.0)
        );
        
        $connection->expects($this->once())
            ->method('insert')
            ->with('employees', [
                'company_id' => 1,
                'employee_name' => 'John Doe',
                'email_address' => 'john@acme.com',
                'salary' => 50000.0
            ]);
        
        $repository->add($command);
    }

    public function testFindAll(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalEmployeeRepository($connection);
        
        $rows = [
            [
                'id' => '1',
                'company_id' => '1',
                'company_name' => 'ACME Corp',
                'employee_name' => 'John Doe',
                'email_address' => 'john@acme.com',
                'salary' => '50000.0'
            ],
            [
                'id' => '2',
                'company_id' => '2',
                'company_name' => 'Tech Corp',
                'employee_name' => 'Jane Smith',
                'email_address' => 'jane@tech.com',
                'salary' => '60000.0'
            ]
        ];
        
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with('SELECT * FROM employees ORDER BY employee_name')
            ->willReturn($rows);
        
        $employees = $repository->findAll();
        
        $this->assertCount(2, $employees);
        $this->assertInstanceOf(Employee::class, $employees[0]);
        $this->assertInstanceOf(Employee::class, $employees[1]);
        $this->assertEquals('John Doe', $employees[0]->employeeName);
        $this->assertEquals('Jane Smith', $employees[1]->employeeName);
    }

    public function testFindAllWithCompanies(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalEmployeeRepository($connection);
        
        $rows = [
            [
                'id' => '1',
                'company_id' => '1',
                'company_name' => 'ACME Corp',
                'employee_name' => 'John Doe',
                'email_address' => 'john@acme.com',
                'salary' => '50000.0'
            ]
        ];
        
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with('SELECT e.*, c.name as company_name FROM employees e JOIN companies c ON e.company_id = c.id ORDER BY e.id')
            ->willReturn($rows);
        
        $employees = $repository->findAllWithCompanies();
        
        $this->assertCount(1, $employees);
        $this->assertInstanceOf(Employee::class, $employees[0]);
    }
}