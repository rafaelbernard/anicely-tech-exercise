<?php

declare(strict_types=1);

namespace App\Employee\Infrastructure\Repository;

use App\Employee\Domain\AddEmployee;
use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeId;
use App\Employee\Domain\EmployeeRepository;
use Doctrine\DBAL\Connection;

final class DbalEmployeeRepository implements EmployeeRepository
{
    public function __construct(private Connection $connection) {}

    public function update(Employee $employee): void
    {
        $this->connection->update(
            'employees',
            [
                'company_id' => $employee->company->id->value,
                'employee_name' => $employee->employeeName,
                'email_address' => $employee->email->value,
                'salary' => $employee->salary->amount
            ],
            ['id' => $employee->id->value]
        );
    }

    public function findById(EmployeeId $id): ?Employee
    {
        $row = $this->connection->fetchAssociative(
            'SELECT e.*, c.name as company_name FROM employees e JOIN companies c ON e.company_id = c.id WHERE e.id = ?',
            [$id->value]
        );

        return $row ? $this->mapToEmployee($row) : null;
    }

    public function findAll(): array
    {
        $result = $this->connection->fetchAllAssociative('SELECT * FROM employees ORDER BY employee_name');
        $employees = [];

        if ($result) {
            foreach ($result as $row) {
                $employees[] = $this->mapToEmployee($row);
            }
        }

        return $employees;
    }

    public function delete(EmployeeId $id): void
    {
        $this->connection->delete('employees', ['id' => $id->value]);
    }

    public function existsByNameAndEmail(string $name, string $email): bool
    {
        $count = $this->connection->fetchOne(
            'SELECT COUNT(*) FROM employees WHERE employee_name = ? AND email_address = ?',
            [$name, $email]
        );
        return $count > 0;
    }

    public function add(AddEmployee $command): void
    {
        $this->connection->insert('employees', [
            'company_id' => $command->companyId->value,
            'employee_name' => $command->employeeName,
            'email_address' => $command->email->value,
            'salary' => $command->salary->amount
        ]);
    }

    public function findAllWithCompanies(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT e.*, c.name as company_name FROM employees e JOIN companies c ON e.company_id = c.id ORDER BY e.id'
        );
        
        $employees = [];
        foreach ($rows as $row) {
            $employees[] = Employee::fromDbRow($row);
        }
        
        return $employees;
    }

    private function mapToEmployee(array $row): Employee
    {
        return Employee::fromDbRow($row);
    }
}
