<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Controller;

use App\Application\Controller\IndexController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class IndexControllerTest extends TestCase
{
    private function createController(): IndexController
    {
        $controller = new IndexController();
        $container = $this->createMock(ContainerInterface::class);
        
        $twig = $this->createMock(Environment::class);
        $twig->method('render')->willReturn('<html>Index Page</html>');
        
        $container->method('has')->willReturn(true);
        $container->method('get')->willReturn($twig);
        
        $controller->setContainer($container);
        return $controller;
    }

    public function testNumberReturnsResponse(): void
    {
        $controller = $this->createController();
        
        $response = $controller->number();
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('<html>Index Page</html>', $response->getContent());
    }
}