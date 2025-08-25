<?php

declare(strict_types=1);

namespace Tests\Unit\Employee\Application\UseCase;

use App\Company\Domain\Company;
use App\Company\Domain\CompanyId;
use App\Company\Domain\CompanyRepository;
use App\Employee\Application\UseCase\DefaultUploadEmployeesFromCsvUseCase;
use App\Employee\Domain\EmployeeRepository;
use PHPUnit\Framework\TestCase;

final class DefaultUploadEmployeesFromCsvUseCaseTest extends TestCase
{
    public function testExecuteProcessesValidCsv(): void
    {
        $employeeRepo = $this->createMock(EmployeeRepository::class);
        $companyRepo = $this->createMock(CompanyRepository::class);
        $useCase = new DefaultUploadEmployeesFromCsvUseCase($employeeRepo, $companyRepo);
        
        $csvContent = "Company Name,Employee Name,Email Address,Salary\nACME Corp,John Doe,john@acme.com,50000";
        
        $company = new Company(new CompanyId(1), 'ACME Corp');
        
        $employeeRepo->expects($this->once())
            ->method('existsByNameAndEmail')
            ->with('John Doe', 'john@acme.com')
            ->willReturn(false);
            
        $companyRepo->expects($this->once())
            ->method('findByName')
            ->with('ACME Corp')
            ->willReturn($company);
            
        $employeeRepo->expects($this->once())
            ->method('add');
        
        $result = $useCase->execute($csvContent);
        
        $this->assertEquals(1, $result['processed']);
        $this->assertEquals(0, $result['skipped']);
        $this->assertEmpty($result['errors']);
    }

    public function testExecuteSkipsDuplicateEmployees(): void
    {
        $employeeRepo = $this->createMock(EmployeeRepository::class);
        $companyRepo = $this->createMock(CompanyRepository::class);
        $useCase = new DefaultUploadEmployeesFromCsvUseCase($employeeRepo, $companyRepo);
        
        $csvContent = "Company Name,Employee Name,Email Address,Salary\nACME Corp,John Doe,john@acme.com,50000";
        
        $employeeRepo->expects($this->once())
            ->method('existsByNameAndEmail')
            ->with('John Doe', 'john@acme.com')
            ->willReturn(true);
            
        $employeeRepo->expects($this->never())
            ->method('add');
        
        $result = $useCase->execute($csvContent);
        
        $this->assertEquals(0, $result['processed']);
        $this->assertEquals(1, $result['skipped']);
        $this->assertEmpty($result['errors']);
    }

    public function testExecuteCreatesNewCompany(): void
    {
        $employeeRepo = $this->createMock(EmployeeRepository::class);
        $companyRepo = $this->createMock(CompanyRepository::class);
        $useCase = new DefaultUploadEmployeesFromCsvUseCase($employeeRepo, $companyRepo);
        
        $csvContent = "Company Name,Employee Name,Email Address,Salary\nNew Corp,Jane Doe,jane@new.com,60000";
        
        $newCompany = new Company(new CompanyId(2), 'New Corp');
        
        $employeeRepo->expects($this->once())
            ->method('existsByNameAndEmail')
            ->willReturn(false);
            
        $companyRepo->expects($this->once())
            ->method('findByName')
            ->with('New Corp')
            ->willReturn(null);
            
        $companyRepo->expects($this->once())
            ->method('insert')
            ->with('New Corp')
            ->willReturn($newCompany);
            
        $employeeRepo->expects($this->once())
            ->method('add');
        
        $result = $useCase->execute($csvContent);
        
        $this->assertEquals(1, $result['processed']);
    }

    public function testExecuteHandlesInvalidFormat(): void
    {
        $employeeRepo = $this->createMock(EmployeeRepository::class);
        $companyRepo = $this->createMock(CompanyRepository::class);
        $useCase = new DefaultUploadEmployeesFromCsvUseCase($employeeRepo, $companyRepo);
        
        $csvContent = "Company Name,Employee Name,Email Address,Salary\nInvalid,Line";
        
        $result = $useCase->execute($csvContent);
        
        $this->assertEquals(0, $result['processed']);
        $this->assertEquals(0, $result['skipped']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Invalid format', $result['errors'][0]);
    }

    public function testItPrintException(): void
    {
        $employeeRepo = $this->createMock(EmployeeRepository::class);
        $companyRepo = $this->createMock(CompanyRepository::class);
        $useCase = new DefaultUploadEmployeesFromCsvUseCase($employeeRepo, $companyRepo);

        $csvContent = "Company Name,Employee Name,Email Address,Salary\nACME Corp,John Doe,john@acme.com,50000";

        $invalidMessage = 'An unexpected exception occurred';
        $employeeRepo->expects($this->once())
                     ->method('existsByNameAndEmail')
                     ->willThrowException(new \Exception($invalidMessage));

        $result = $useCase->execute($csvContent);

        $this->assertEquals(0, $result['processed']);
        $this->assertEquals(0, $result['skipped']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString($invalidMessage, $result['errors'][0]);
    }
}