<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Security\ApiTokenAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class ApiTokenAuthenticatorTest extends TestCase
{
    public function testSupportsWithoutApiTokenRequired(): void
    {
        $authenticator = new ApiTokenAuthenticator('test-token', false);
        $request = new Request();
        
        $this->assertTrue($authenticator->supports($request));
    }

    public function testSupportsWithApiTokenRequired(): void
    {
        $authenticator = new ApiTokenAuthenticator('test-token', true);
        
        $requestWithToken = new Request();
        $requestWithToken->headers->set('X-API-TOKEN', 'test-token');
        
        $requestWithoutToken = new Request();
        
        $this->assertTrue($authenticator->supports($requestWithToken));
        $this->assertFalse($authenticator->supports($requestWithoutToken));
    }

    public function testAuthenticateWithoutApiTokenRequired(): void
    {
        $authenticator = new ApiTokenAuthenticator('test-token', false);
        $request = new Request();
        
        $passport = $authenticator->authenticate($request);
        
        $this->assertInstanceOf(SelfValidatingPassport::class, $passport);
    }

    public function testAuthenticateWithValidToken(): void
    {
        $authenticator = new ApiTokenAuthenticator('test-token', true);
        $request = new Request();
        $request->headers->set('X-API-TOKEN', 'test-token');
        
        $passport = $authenticator->authenticate($request);
        
        $this->assertInstanceOf(SelfValidatingPassport::class, $passport);
    }

    public function testAuthenticateWithInvalidToken(): void
    {
        $authenticator = new ApiTokenAuthenticator('test-token', true);
        $request = new Request();
        $request->headers->set('X-API-TOKEN', 'wrong-token');
        
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Invalid API token');
        
        $authenticator->authenticate($request);
    }

    public function testAuthenticateWithMissingToken(): void
    {
        $authenticator = new ApiTokenAuthenticator('test-token', true);
        $request = new Request();
        
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('API token not found');
        
        $authenticator->authenticate($request);
    }

    public function testOnAuthenticationSuccess(): void
    {
        $authenticator = new ApiTokenAuthenticator('test-token');
        $request = new Request();
        $token = $this->createMock(TokenInterface::class);
        
        $result = $authenticator->onAuthenticationSuccess($request, $token, 'main');
        
        $this->assertNull($result);
    }
}
