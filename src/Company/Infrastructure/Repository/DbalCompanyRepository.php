<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Repository;

use App\Company\Domain\Company;
use App\Company\Domain\CompanyId;
use App\Company\Domain\CompanyRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class DbalCompanyRepository implements CompanyRepository
{
    public function __construct(private Connection $connection) {}

    /**
     * @throws Exception
     */
    public function insert($name): Company
    {
        $this->connection->insert('companies', ['name' => $name]);
        return new Company(new CompanyId((int) $this->connection->lastInsertId()), $name);
    }

    public function findById(CompanyId $id): ?Company
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM companies WHERE id = ?',
            [$id->value]
        );

        return $row ? $this->mapToCompany($row) : null;
    }

    public function findByName(string $name): ?Company
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM companies WHERE name = ?',
            [$name]
        );

        return $row ? $this->mapToCompany($row) : null;
    }

    private function mapToCompany(array $row): Company
    {
        return Company::fromDbRow($row);
    }

    public function getCompanyAverageSalaries(): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT c.name as company_name, AVG(e.salary) as average_salary FROM employees e JOIN companies c ON e.company_id = c.id GROUP BY c.id, c.name ORDER BY c.name'
        );
    }
}
