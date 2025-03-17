<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use App\Models\Order;

class OrderRepository
{
    public function findByUuid(string $uuid): Order|null
    {
        /** @var Order|null */
        return Order::query()
            ->where('uuid', $uuid)
            ->first()
        ;
    }

    /**
     * @return Order[]
     */
    public function getByStatuses(array $statuses): array
    {
        return Order::query()
            ->whereIn('status', $statuses)
            ->get()
            ->all()
        ;
    }
}
