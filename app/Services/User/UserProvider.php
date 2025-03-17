<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use Illuminate\Auth\AuthManager;
use LogicException;

class UserProvider
{
    public function __construct(private readonly AuthManager $authManager)
    {
    }

    public function getAuthenticated(): User
    {
        $user = $this->authManager->user();
        if (!$user instanceof User) {
            throw new LogicException(sprintf('Authenticated user must be instance of %s', User::class));
        }

        return $user;
    }
}
