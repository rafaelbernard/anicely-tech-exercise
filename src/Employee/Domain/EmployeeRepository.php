<?php

declare(strict_types=1);

namespace App\Employee\Domain;

# TODO: We could apply CQRS (QueryServices and CommandHandlers) if we need to separate the read and write operations
interface EmployeeRepository
{
    public function findById(EmployeeId $id): ?Employee;
    public function findAll(): array;
    public function delete(EmployeeId $id): void;
    public function findAllWithCompanies(): array;
    public function existsByNameAndEmail(string $name, string $email): bool;
    public function add(AddEmployee $command): void;
    public function update(Employee $employee): void;
}
