<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(private readonly string $apiToken, private readonly bool $useApiToken = false)
    {
    }
    
    public function supports(Request $request): ?bool
    {
        if (!$this->useApiToken) {
            return true;
        }


        return $request->headers->has('X-API-TOKEN');
    }

    public function authenticate(Request $request): Passport
    {
        if (!$this->useApiToken) {
            return new SelfValidatingPassport(
                new UserBadge('api_client', function() {
                    return new ApiUser();
                })
            );
        }

        $apiToken = $request->headers->get('X-API-TOKEN');
        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('API token not found');
        }

        // Check if the token matches the expected value
        if ($apiToken !== $this->apiToken) {
            throw new CustomUserMessageAuthenticationException('Invalid API token');
        }

        return new SelfValidatingPassport(
            new UserBadge('api_client', function() {
                return new ApiUser();
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => 'Authentication failed: ' . $exception->getMessage()
        ];
        
        // Check Accept header to determine response format
        $acceptHeader = $request->headers->get('Accept');
        
        // Default to JSON for API routes
        if (str_starts_with($request->getPathInfo(), '/api/') || 
            (str_contains($acceptHeader, 'application/json') || str_contains($acceptHeader, 'application/ld+json'))) {
            return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
        }
        
        // For HTML requests, return a simple unauthorized response
        return new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
    }
}
