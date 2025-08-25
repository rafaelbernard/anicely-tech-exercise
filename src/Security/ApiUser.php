<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

readonly class ApiUser implements UserInterface
{
    public function __construct(private string $apiToken = 'api_client', private array $roles = ['ROLE_API'])
    {
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // Nothing to erase
    }

    public function getUserIdentifier(): string
    {
        return $this->apiToken;
    }
}
