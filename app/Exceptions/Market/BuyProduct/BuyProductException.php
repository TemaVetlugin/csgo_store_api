<?php

declare(strict_types=1);

namespace App\Exceptions\Market\BuyProduct;

use App\Exceptions\Market\BuyProduct\Case\BuyProductExceptionCase;
use RuntimeException;

class BuyProductException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly BuyProductExceptionCase $case,
        private readonly array $products = [],
    ) {
        parent::__construct($message);
    }

    public function getCase(): BuyProductExceptionCase
    {
        return $this->case;
    }

    public function getProducts(): array
    {
        return $this->products;
    }
}
