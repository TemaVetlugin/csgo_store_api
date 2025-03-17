<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /** @var Order */
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
            'user_id' => $this->resource->getUser()->getId(),
            'payment_id' => $this->resource->getPayment()?->getId(),
            'total_price' => $this->resource->getTotalPrice(),
            'total_price_currency' => $this->resource->getTotalPriceCurrency(),
            'status' => $this->resource->getStatus()->toString(),
            'created_at' => $this->resource->getCreatedAt()->toDateTimeString(),
            'transactions' => TransactionResource::collection($this->resource->getTransactions()),
        ];
    }
}
