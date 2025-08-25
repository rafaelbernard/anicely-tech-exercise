<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Controller;

use App\Application\Controller\EmployeeController;
use App\Application\Validator\GlobalFileDataCsvValidator;
use App\Company\Domain\Company;
use App\Company\Domain\CompanyId;
use App\Core\Domain\Email;
use App\Employee\Domain\Application\UpdateEmployeeEmailUseCase;
use App\Employee\Domain\Application\UploadEmployeesFromCsvUseCase;
use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeId;
use App\Employee\Domain\EmployeeRepository;
use App\Employee\Domain\Money;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Twig\Environment;

/**
 * It could be better to be at Integration test as it needs Session
 * Create here to have minimum tests
 * Another option is to mock methods that depends on session, such as `addFlash`
 */
final class EmployeeControllerTest extends TestCase
{
    private function createController(
        EmployeeRepository $repository,
        UpdateEmployeeEmailUseCase $updateEmailUseCase,
        UploadEmployeesFromCsvUseCase $uploadCsvUseCase,
        GlobalFileDataCsvValidator $csvValidator
    ): EmployeeController {
        $controller = new EmployeeController(
            $repository,
            $updateEmailUseCase,
            $uploadCsvUseCase,
            5, // csvMaxSizeMB
            $csvValidator
        );
        
        $container = $this->createMock(ContainerInterface::class);
        $twig = $this->createMock(Environment::class);
        $twig->method('render')->willReturn('<html>Employee Page</html>');
        
        $container->method('has')->willReturn(true);
        $container->method('get')->willReturn($twig);

        $controller->setContainer($container);
        return $controller;
    }

    public function testListReturnsResponse(): void
    {
        $repository = $this->createMock(EmployeeRepository::class);
        $updateEmailUseCase = $this->createMock(UpdateEmployeeEmailUseCase::class);
        $uploadCsvUseCase = $this->createMock(UploadEmployeesFromCsvUseCase::class);
        $csvValidator = $this->createMock(GlobalFileDataCsvValidator::class);
        
        $employees = [$this->makeEmployee()];
        
        $repository->expects($this->once())
            ->method('findAllWithCompanies')
            ->willReturn($employees);
        
        $controller = $this->createController($repository, $updateEmailUseCase, $uploadCsvUseCase, $csvValidator);
        
        $response = $controller->list();
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteRedirectsToList(): void
    {
        $this->markTestSkipped('Good to be implemented as Integration Test');

        $repository = $this->createMock(EmployeeRepository::class);
        $updateEmailUseCase = $this->createMock(UpdateEmployeeEmailUseCase::class);
        $uploadCsvUseCase = $this->createMock(UploadEmployeesFromCsvUseCase::class);
        $csvValidator = $this->createMock(GlobalFileDataCsvValidator::class);
        
        $repository->expects($this->once())
            ->method('delete')
            ->with($this->equalTo(new EmployeeId(1)));
        
        $controller = $this->createController($repository, $updateEmailUseCase, $uploadCsvUseCase, $csvValidator);
        
        $response = $controller->delete(1);
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testUpdateEmailReturnsSuccessJson(): void
    {
        $this->markTestSkipped('Good to be implemented as Integration Test');

        $repository = $this->createMock(EmployeeRepository::class);
        $updateEmailUseCase = $this->createMock(UpdateEmployeeEmailUseCase::class);
        $uploadCsvUseCase = $this->createMock(UploadEmployeesFromCsvUseCase::class);
        $csvValidator = $this->createMock(GlobalFileDataCsvValidator::class);
        
        $request = new Request();
        $request->request->set('email', 'test@example.com');
        
        $updateEmailUseCase->expects($this->once())
            ->method('execute')
            ->with(1, $this->equalTo(new Email('test@example.com')));
        
        $controller = $this->createController($repository, $updateEmailUseCase, $uploadCsvUseCase, $csvValidator);
        
        $response = $controller->updateEmail(1, $request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
    }

    public function testUpdateEmailReturnsErrorJson(): void
    {
        $repository = $this->createMock(EmployeeRepository::class);
        $updateEmailUseCase = $this->createMock(UpdateEmployeeEmailUseCase::class);
        $uploadCsvUseCase = $this->createMock(UploadEmployeesFromCsvUseCase::class);
        $csvValidator = $this->createMock(GlobalFileDataCsvValidator::class);
        
        $request = new Request();
        $request->request->set('email', 'invalid-email');
        
        $controller = $this->createController($repository, $updateEmailUseCase, $uploadCsvUseCase, $csvValidator);
        
        $response = $controller->updateEmail(1, $request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertArrayHasKey('error', $data);
    }

    public function testUploadGetReturnsResponse(): void
    {
        $repository = $this->createMock(EmployeeRepository::class);
        $updateEmailUseCase = $this->createMock(UpdateEmployeeEmailUseCase::class);
        $uploadCsvUseCase = $this->createMock(UploadEmployeesFromCsvUseCase::class);
        $csvValidator = $this->createMock(GlobalFileDataCsvValidator::class);
        
        $request = new Request();
        
        $controller = $this->createController($repository, $updateEmailUseCase, $uploadCsvUseCase, $csvValidator);
        
        $response = $controller->upload($request);
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    private function makeEmployee(): Employee
    {
        $id = new EmployeeId(1);
        $company = new Company(new CompanyId(1), 'ACME Corp');
        $email = new Email('john@acme.com');
        $salary = new Money(50000.0);

        return new Employee($id, $company, 'John Doe', $email, $salary);
    }
}
