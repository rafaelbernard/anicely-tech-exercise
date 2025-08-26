<?php

declare(strict_types=1);

namespace App\Employee\Domain;

use App\Company\Domain\Company;
use App\Core\Domain\Email;

final readonly class Employee
{
    public function __construct(
        public EmployeeId $id,
        public Company $company,
        public string $employeeName,
        public Email $email,
        public Money $salary
    ) {}

    public static function fromDbRow(array $row): Employee
    {
        return new Employee(
            new EmployeeId((int) $row['id']),
            Company::fromFkDbRow($row),
            $row['employee_name'],
            new Email($row['email_address']),
            new Money((float) $row['salary'])
        );
    }
}
