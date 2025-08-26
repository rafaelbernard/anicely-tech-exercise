<?php

declare(strict_types=1);

namespace App\Employee\Application\UseCase;

use App\Core\Domain\Email;
use App\Employee\Domain\Application\UpdateEmployeeEmailUseCase;
use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeId;
use App\Employee\Domain\EmployeeRepository;
use InvalidArgumentException;

final readonly class DefaultUpdateEmployeeEmailUseCase implements UpdateEmployeeEmailUseCase
{
    public function __construct(private EmployeeRepository $repository) {}

    public function execute(int $employeeId, Email $email): void
    {
        $employee = $this->repository->findById(new EmployeeId($employeeId));
        
        if (!$employee) {
            throw new InvalidArgumentException('Employee not found');
        }

        $updatedEmployee = new Employee(
            $employee->id,
            $employee->company,
            $employee->employeeName,
            $email,
            $employee->salary
        );

        $this->repository->update($updatedEmployee);
    }
}