<?php

declare(strict_types=1);

namespace App\Services\Transaction;

use App\Entity\DTO\Filter\GetTransactionsFilter;
use App\Repositories\User\TransactionRepository;

class TransactionProvider
{
    public function __construct(private readonly TransactionRepository $transactionRepository)
    {
    }

    public function getByFilter(GetTransactionsFilter $filter): array
    {
        return $this->transactionRepository->getByFilter($filter);
    }
}
