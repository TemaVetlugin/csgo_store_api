<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /** @var Transaction */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getId(),
            'uuid' => $this->resource->getUuid(),
            'type' => $this->resource->getType()->toString(),
            'status' => $this->resource->getStatus()?->toString(),
            'product' => [
                'id' => $this->resource->getProductId(),
                'name' => $this->resource->getProductName(),
                'original_price' => $this->resource->getProductOriginalPrice(),
            ],
            'buy_id' => $this->resource->getBuyId(),
            'payed_amount' => $this->resource->getPayedAmount(),
            'payed_in_currency' => $this->resource->getPayedInCurrency(),
            'created_at' => $this->resource->getCreatedAt()->toDateTimeString(),
        ];
    }
}
