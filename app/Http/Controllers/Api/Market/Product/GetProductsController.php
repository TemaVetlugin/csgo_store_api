<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\Product;

use App\Http\Requests\Api\Market\Product\GetProductsRequest;
use App\Http\Resources\Api\ProductResource;
use App\Services\Market\Provider\ProductProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class GetProductsController extends Controller
{
    public function __construct(private readonly ProductProvider $productProvider)
    {
    }

    public function __invoke(GetProductsRequest $request): JsonResponse
    {
        $products = $this->productProvider->getByFilter($request->getFilter());

        return response()->json(['products' => ProductResource::collection($products)]);
    }
}
