<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Http\Requests\Api\User\GetUserTransactionsRequest;
use App\Http\Resources\Api\TransactionResource;
use App\Services\Transaction\TransactionProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class GetUserTransactionsController extends Controller
{
    public function __construct(private readonly TransactionProvider $transactionProvider)
    {
    }

    public function __invoke(GetUserTransactionsRequest $request): JsonResponse
    {
        $transactions = $this->transactionProvider->getByFilter($request->getFilter());

        return response()->json(['transactions' => TransactionResource::collection($transactions)]);
    }
}
