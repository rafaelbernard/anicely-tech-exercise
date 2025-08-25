<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Controller;

use App\Application\Controller\CompanyController;
use App\Company\Domain\CompanyRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class CompanyControllerTest extends TestCase
{
    private function createController(CompanyRepository $repository): CompanyController
    {
        $controller = new CompanyController($repository);
        $container = $this->createMock(ContainerInterface::class);
        
        $twig = $this->createMock(Environment::class);
        $twig->method('render')->willReturn('<html>Companies Page</html>');
        
        $container->method('has')->willReturn(true);
        $container->method('get')->willReturn($twig);
        
        $controller->setContainer($container);
        return $controller;
    }

    public function testCompanyAveragesReturnsResponse(): void
    {
        $repository = $this->createMock(CompanyRepository::class);
        $companies = [
            ['company_name' => 'ACME Corp', 'average_salary' => '75000.00'],
            ['company_name' => 'Tech Solutions', 'average_salary' => '65000.00']
        ];
        
        $repository->expects($this->once())
            ->method('getCompanyAverageSalaries')
            ->willReturn($companies);
        
        $controller = $this->createController($repository);
        
        $response = $controller->companyAverages();
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('<html>Companies Page</html>', $response->getContent());
    }
}