<?php

declare(strict_types=1);

namespace App\Entity\DTO\Filter;

class GetProductsFilter
{
    private int $page = 1;
    private int $perPage = 15;

    private string|null $name = null;
    private float|null $priceMin = null;
    private float|null $priceMax = null;

    /** @var string[] */
    private array $ids = [];

    /** @var string[] */
    private array $types = [];

    /** @var string[] */
    private array $exteriors = [];

    /** @var string[] */
    private array $rarities = [];

    /** @var string[] */
    private array $qualities = [];

    /** @var string[] */
    private array $weapons = [];

    /** @var string[] */
    private array $stickerNames = [];

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

    public function getName(): string|null
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPriceMin(): float|null
    {
        return $this->priceMin;
    }

    public function setPriceMin(float $priceMin): static
    {
        $this->priceMin = $priceMin;

        return $this;
    }

    public function getPriceMax(): float|null
    {
        return $this->priceMax;
    }

    public function setPriceMax(float $priceMax): static
    {
        $this->priceMax = $priceMax;

        return $this;
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function setIds(array $ids): static
    {
        $this->ids = $ids;

        return $this;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function setTypes(array $types): static
    {
        $this->types = $types;

        return $this;
    }

    public function getExteriors(): array
    {
        return $this->exteriors;
    }

    public function setExteriors(array $exteriors): static
    {
        $this->exteriors = $exteriors;

        return $this;
    }

    public function getRarities(): array
    {
        return $this->rarities;
    }

    public function setRarities(array $rarities): static
    {
        $this->rarities = $rarities;

        return $this;
    }

    public function getQualities(): array
    {
        return $this->qualities;
    }

    public function setQualities(array $qualities): static
    {
        $this->qualities = $qualities;

        return $this;
    }

    public function getWeapons(): array
    {
        return $this->weapons;
    }

    public function setWeapons(array $weapons): static
    {
        $this->weapons = $weapons;

        return $this;
    }

    public function getStickerNames(): array
    {
        return $this->stickerNames;
    }

    public function setStickerNames(array $stickerNames): static
    {
        $this->stickerNames = $stickerNames;

        return $this;
    }
}
