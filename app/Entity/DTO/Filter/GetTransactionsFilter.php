<?php

declare(strict_types=1);

namespace App\Entity\DTO\Filter;

use App\Models\Enum\TransactionStatus;
use App\Models\Enum\TransactionType;

class GetTransactionsFilter
{
    private int $page = 1;
    private int $perPage = 5;

    private int|null $userId = null;
    private string|null $name = null;
    private TransactionType|null $type = null;

    /** @var TransactionStatus[] */
    private array $statuses = [];

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage): static
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function getUserId(): int|null
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getName(): string|null
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): TransactionType|null
    {
        return $this->type;
    }

    public function setType(TransactionType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStatuses(): array
    {
        return $this->statuses;
    }

    public function setStatuses(array $statuses): static
    {
        $this->statuses = $statuses;

        return $this;
    }
}
