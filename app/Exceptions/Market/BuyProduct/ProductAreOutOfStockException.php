<?php

declare(strict_types=1);

namespace App\Exceptions\Market\BuyProduct;

use App\Exceptions\Market\BuyProduct\Case\BuyProductExceptionCase;

class ProductAreOutOfStockException extends BuyProductException
{
    public function __construct(
        string $message,
        private readonly array $outOfStockProductIds,
    ) {
        parent::__construct($message, BuyProductExceptionCase::ProductsAreOutOfStock);
    }

    public function getOutOfStockProductIds(): array
    {
        return $this->outOfStockProductIds;
    }

    public function getContext(): array
    {
        return [
            'products_out_of_stock' => $this->outOfStockProductIds
        ];
    }
}
