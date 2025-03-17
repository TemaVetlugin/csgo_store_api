<?php

declare(strict_types=1);

namespace App\Exceptions\Market\User;

use App\Exceptions\Market\User\Case\UserIsNotTradableExceptionCase;
use RuntimeException;

class UserIsNotTradableException extends RuntimeException
{
    public function __construct(string $message, private readonly UserIsNotTradableExceptionCase $case)
    {
        parent::__construct($message);
    }

    public function getCase(): UserIsNotTradableExceptionCase
    {
        return $this->case;
    }
}
