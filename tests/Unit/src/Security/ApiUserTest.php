<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Security\ApiUser;
use PHPUnit\Framework\TestCase;

final class ApiUserTest extends TestCase
{
    public function testDefaultConstruction(): void
    {
        $user = new ApiUser();
        
        $this->assertEquals('api_client', $user->getUserIdentifier());
        $this->assertEquals(['ROLE_API'], $user->getRoles());
    }

    public function testCustomConstruction(): void
    {
        $user = new ApiUser('custom_token', ['ROLE_ADMIN']);
        
        $this->assertEquals('custom_token', $user->getUserIdentifier());
        $this->assertEquals(['ROLE_ADMIN'], $user->getRoles());
    }

    public function testEraseCredentials(): void
    {
        // Should not throw any exception
        $this->expectNotToPerformAssertions();

        $user = new ApiUser();
        // This methd will be deprecated soon
        $user->eraseCredentials();
    }
}
