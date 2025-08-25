<?php

declare(strict_types=1);

namespace Tests\Unit\Api\Controller;

use App\Api\Controller\ApiController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ApiControllerTest extends TestCase
{
    private function createController(): ApiController
    {
        $controller = new ApiController();
        $container = $this->createMock(ContainerInterface::class);
        $controller->setContainer($container);
        return $controller;
    }

    public function testStatusReturnsJsonResponse(): void
    {
        $controller = $this->createController();
        
        $response = $controller->status();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testStatusReturnsCorrectData(): void
    {
        $controller = $this->createController();
        
        $response = $controller->status();
        $data = json_decode($response->getContent(), true);
        
        $this->assertEquals('ok', $data['status']);
        $this->assertArrayHasKey('timestamp', $data);
        $this->assertInstanceOf(\DateTime::class, new \DateTime($data['timestamp']['date']));
    }
}