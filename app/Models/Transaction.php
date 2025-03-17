<?php

declare(strict_types=1);

namespace App\Models;

use App\Jobs\SendSuccessTransactionMailJob;
use App\Models\Enum\TransactionStatus;
use App\Models\Enum\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Transaction extends Model
{
    protected $casts = [
        'type' => TransactionType::class,
        'status' => TransactionStatus::class,
        'buy_id' => 'int',
        'product_original_price' => 'double',
        'payed_amount' => 'double',
    ];

//    protected static function booted()
//    {
//        static::updated(function ($transaction) {
//            if ($transaction->isDirty('status')) {
//                $newStatus = $transaction->status;
//                Log::error('111111111123' );
//                try {
//                    Log::error('321' );
//                    if ($newStatus === 'waiting_accept'||$newStatus === 'user_not_tradable') {
//                        Log::error('333' );
//                        SendSuccessTransactionMailJob::dispatch($transaction->id);
//                    }
//                } catch (\Exception $e) {
//                    Log::error('Failed to send success transaction email: ' . $e->getMessage());
//                }
//            }
//        });
//    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): static
    {
        $this->order_id = $order->getId();

        $this->setRelation('order', $order);

        return $this;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function setType(TransactionType $transactionType): static
    {
        $this->type = $transactionType;

        return $this;
    }

    public function getStatus(): TransactionStatus|null
    {
        return $this->status;
    }

    public function setStatus(TransactionStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getProductId(): string
    {
        return $this->product_id;
    }

    public function setProductId(string $productId): static
    {
        $this->product_id = $productId;

        return $this;
    }

    public function getProductName(): string
    {
        return $this->product_name;
    }

    public function setProductName(string $productName): static
    {
        $this->product_name = $productName;

        return $this;
    }

    public function getProductOriginalPrice(): float
    {
        return $this->product_original_price;
    }

    public function setProductOriginalPrice(float $productOriginalPrice): static
    {
        $this->product_original_price = $productOriginalPrice;

        return $this;
    }

    public function getBuyId(): int|null
    {
        return $this->buy_id;
    }

    public function setBuyId(int $buyId): static
    {
        $this->buy_id = $buyId;

        return $this;
    }

    public function getPayedAmount(): float|null
    {
        return $this->payed_amount;
    }

    public function setPayedAmount(float $payedAmount): static
    {
        $this->payed_amount = $payedAmount;

        return $this;
    }

    public function getPayedInCurrency(): string|null
    {
        return $this->payed_in_currency;
    }

    public function setPayedInCurrency(string $payedInCurrency): static
    {
        $this->payed_in_currency = $payedInCurrency;

        return $this;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    protected function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
