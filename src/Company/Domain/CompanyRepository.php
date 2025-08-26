<?php

declare(strict_types=1);

namespace App\Company\Domain;

interface CompanyRepository
{
    public function findById(CompanyId $id): ?Company;
    public function findByName(string $name): ?Company;
    public function insert($name): Company;
    public function getCompanyAverageSalaries(): array;
}
