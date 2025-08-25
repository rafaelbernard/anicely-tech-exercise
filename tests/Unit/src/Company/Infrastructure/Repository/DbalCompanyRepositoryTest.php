<?php

declare(strict_types=1);

namespace Tests\Unit\Company\Infrastructure\Repository;

use App\Company\Domain\Company;
use App\Company\Domain\CompanyId;
use App\Company\Infrastructure\Repository\DbalCompanyRepository;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class DbalCompanyRepositoryTest extends TestCase
{
    public function testInsert(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalCompanyRepository($connection);
        
        $connection->expects($this->once())
            ->method('insert')
            ->with('companies', ['name' => 'New Company']);
            
        $connection->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('5');
        
        $company = $repository->insert('New Company');
        
        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals(5, $company->id->value);
        $this->assertEquals('New Company', $company->name);
    }

    public function testFindByIdReturnsCompany(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalCompanyRepository($connection);
        
        $row = [
            'id' => '1',
            'name' => 'ACME Corp'
        ];
        
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->with('SELECT * FROM companies WHERE id = ?', [1])
            ->willReturn($row);
        
        $company = $repository->findById(new CompanyId(1));
        
        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals(1, $company->id->value);
        $this->assertEquals('ACME Corp', $company->name);
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalCompanyRepository($connection);
        
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->willReturn(false);
        
        $company = $repository->findById(new CompanyId(999));
        
        $this->assertNull($company);
    }

    public function testFindByNameReturnsCompany(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalCompanyRepository($connection);
        
        $row = [
            'id' => '2',
            'name' => 'Tech Solutions'
        ];
        
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->with('SELECT * FROM companies WHERE name = ?', ['Tech Solutions'])
            ->willReturn($row);
        
        $company = $repository->findByName('Tech Solutions');
        
        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals(2, $company->id->value);
        $this->assertEquals('Tech Solutions', $company->name);
    }

    public function testFindByNameReturnsNullWhenNotFound(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalCompanyRepository($connection);
        
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->willReturn(false);
        
        $company = $repository->findByName('Non-existent Company');
        
        $this->assertNull($company);
    }

    public function testGetCompanyAverageSalaries(): void
    {
        $connection = $this->createMock(Connection::class);
        $repository = new DbalCompanyRepository($connection);
        
        $expectedData = [
            ['company_name' => 'ACME Corp', 'average_salary' => '75000.00'],
            ['company_name' => 'Tech Solutions', 'average_salary' => '65000.00']
        ];
        
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with('SELECT c.name as company_name, AVG(e.salary) as average_salary FROM employees e JOIN companies c ON e.company_id = c.id GROUP BY c.id, c.name ORDER BY c.name')
            ->willReturn($expectedData);
        
        $result = $repository->getCompanyAverageSalaries();
        
        $this->assertEquals($expectedData, $result);
    }
}