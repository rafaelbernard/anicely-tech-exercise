<?php

declare(strict_types=1);

namespace Tests\Unit\Employee\Application\UseCase;

use App\Company\Domain\Company;
use App\Company\Domain\CompanyId;
use App\Core\Domain\Email;
use App\Employee\Application\UseCase\DefaultUpdateEmployeeEmailUseCase;
use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeId;
use App\Employee\Domain\EmployeeRepository;
use App\Employee\Domain\Money;
use PHPUnit\Framework\TestCase;

final class DefaultUpdateEmployeeEmailUseCaseTest extends TestCase
{
    public function testExecuteUpdatesEmployeeEmail(): void
    {
        $repository = $this->createMock(EmployeeRepository::class);
        $useCase = new DefaultUpdateEmployeeEmailUseCase($repository);
        
        $employee = new Employee(
            new EmployeeId(1),
            new Company(new CompanyId(1), 'ACME Corp'),
            'John Doe',
            new Email('old@example.com'),
            new Money(50000.0)
        );
        
        $newEmail = new Email('new@example.com');
        
        $repository->expects($this->once())
            ->method('findById')
            ->with($this->equalTo(new EmployeeId(1)))
            ->willReturn($employee);
            
        $repository->expects($this->once())
            ->method('update')
            ->with($this->callback(function (Employee $updatedEmployee) use ($newEmail) {
                return $updatedEmployee->email->value === $newEmail->value;
            }));
        
        $useCase->execute(1, $newEmail);
    }

    public function testExecuteThrowsExceptionWhenEmployeeNotFound(): void
    {
        $repository = $this->createMock(EmployeeRepository::class);
        $useCase = new DefaultUpdateEmployeeEmailUseCase($repository);
        
        $repository->expects($this->once())
            ->method('findById')
            ->with($this->equalTo(new EmployeeId(999)))
            ->willReturn(null);
            
        $repository->expects($this->never())
            ->method('update');
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Employee not found');
        
        $useCase->execute(999, new Email('test@example.com'));
    }
}