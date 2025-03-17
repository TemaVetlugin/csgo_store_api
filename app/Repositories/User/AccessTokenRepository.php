<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User;

class AccessTokenRepository
{
    public function deleteAllForUser(User $user): void
    {
        $user->tokens()->delete();
    }
}
