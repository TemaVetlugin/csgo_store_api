<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\Product;

use App\Http\Resources\Api\ProductResource;
use App\Services\Market\Provider\ProductProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class GetPopularProductsController extends Controller
{
    public function __construct(private readonly ProductProvider $productProvider)
    {
    }

    public function __invoke(): JsonResponse
    {
        $products = $this->productProvider->getPopular();

        return response()->json(['products' => ProductResource::collection($products)]);
    }
}
