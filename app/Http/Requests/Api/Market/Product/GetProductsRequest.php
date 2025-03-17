<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Market\Product;

use App\Entity\DTO\Filter\GetProductsFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetProductsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'required', 'int', 'min:0'],
            'per_page' => ['sometimes', 'required', 'int', 'min:1', 'max:30'],

            'name' => ['sometimes', 'required', 'string', 'min:1'],
            'price_min' => ['sometimes', 'required', 'numeric', 'min:0'],
            'price_max' => ['sometimes', 'required', 'numeric', 'min:0'],

            'ids' => ['sometimes', 'required', 'array', 'min:1'],
            'ids.*' => ['required', 'string', 'numeric', 'distinct', 'min:1'],

            'types' => ['sometimes', 'required', 'array', 'min:1'],
            'types.*' => ['required', 'string', 'distinct', 'min:1'],

            'exteriors' => ['sometimes', 'required', 'array', 'min:1'],
            'exteriors.*' => ['required', 'string', 'distinct', 'min:1'],

            'rarities' => ['sometimes', 'required', 'array', 'min:1'],
            'rarities.*' => ['required', 'string', 'distinct', 'min:1'],

            'qualities' => ['sometimes', 'required', 'array', 'min:1'],
            'qualities.*' => ['required', 'string', 'distinct', 'min:1'],

            'weapons' => ['sometimes', 'required', 'array', 'min:1'],
            'weapons.*' => ['required', 'string', 'distinct', 'min:1'],

            'stickers' => ['sometimes', 'required', 'array', 'min:1'],
            'stickers.*' => ['required', 'string', 'distinct', 'min:1'],
        ];
    }

    public function getFilter(): GetProductsFilter
    {
        $validatedParameters = $this->validated();

        $filter = new GetProductsFilter();

        if (isset($validatedParameters['page'])) {
            $filter->setPage((int) $validatedParameters['page']);
        }

        if (isset($validatedParameters['per_page'])) {
            $filter->setPerPage((int) $validatedParameters['per_page']);
        }

        if (isset($validatedParameters['name'])) {
            $filter->setName($validatedParameters['name']);
        }

        if (isset($validatedParameters['price_min'])) {
            $filter->setPriceMin((float) $validatedParameters['price_min']);
        }

        if (isset($validatedParameters['price_max'])) {
            $filter->setPriceMax((float) $validatedParameters['price_max']);
        }

        if (isset($validatedParameters['ids'])) {
            $filter->setIds($validatedParameters['ids']);
        }

        if (isset($validatedParameters['types'])) {
            $filter->setTypes($validatedParameters['types']);
        }

        if (isset($validatedParameters['exteriors'])) {
            $filter->setExteriors($validatedParameters['exteriors']);
        }

        if (isset($validatedParameters['rarities'])) {
            $filter->setRarities($validatedParameters['rarities']);
        }

        if (isset($validatedParameters['qualities'])) {
            $filter->setQualities($validatedParameters['qualities']);
        }

        if (isset($validatedParameters['weapons'])) {
            $filter->setWeapons($validatedParameters['weapons']);
        }

        if (isset($validatedParameters['stickers'])) {
            $filter->setStickerNames($validatedParameters['stickers']);
        }

        return $filter;
    }
}
