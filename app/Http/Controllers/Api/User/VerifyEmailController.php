<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Http\Requests\Api\User\GetUserTransactionsRequest;
use App\Http\Resources\Api\TransactionResource;
use App\Services\Transaction\TransactionProvider;
use App\Services\User\UserProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailController extends Controller
{
    public function __construct(private readonly UserProvider $userProvider,)
    {
    }

    public function __invoke(GetUserTransactionsRequest $request): JsonResponse
    {
        $user = $this->userProvider->getAuthenticated();
        if ($user->getEmail() !== null) {
            $url = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );
            $user->sendEmailVerificationNotification();
        }

        return response()->json();
    }
}
