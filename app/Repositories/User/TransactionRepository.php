<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Entity\DTO\Filter\GetTransactionsFilter;
use App\Models\Transaction;

class TransactionRepository
{
    public function find(int $id): Transaction|null
    {
        return Transaction::find($id);
    }

    public function findByUuid(string $transactionUuid): Transaction|null
    {
        /** @var Transaction */
        return Transaction::query()
            ->where('uuid', $transactionUuid)
            ->first()
        ;
    }

    public function getByFilter(GetTransactionsFilter $filter): array
    {
        $queryBuilder = Transaction::query()
            ->select('transactions.*')
        ;

        if ($filter->getUserId() !== null) {
            $queryBuilder->join('orders', 'transactions.order_id', '=', 'orders.id');
            $queryBuilder->where('orders.user_id', $filter->getUserId());
        }

        if ($filter->getName() !== null) {
            $queryBuilder->where('transactions.product_name', 'like', '%'.$filter->getName().'%');
        }

        if ($filter->getType() !== null) {
            $queryBuilder->where('transactions.type', $filter->getType());
        }

        if (count($filter->getStatuses()) > 0) {
            $queryBuilder->whereIn('transactions.status', $filter->getStatuses());
        }

        return $queryBuilder
            ->limit($filter->getPerPage())
            ->offset($filter->getPerPage() * ($filter->getPage() - 1))
            ->get()
            ->all()
        ;
    }
}
