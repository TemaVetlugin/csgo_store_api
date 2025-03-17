<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Http\Requests\Api\User\CompleteUserRegistrationRequest;
use App\Http\Resources\Api\UserProfileResource;
use App\Services\User\UserManager;
use App\Services\User\UserProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CompleteUserRegistrationController extends Controller
{
    public function __construct(
        private readonly UserProvider $userProvider,
        private readonly UserManager $userManager,
    ) {
    }

    public function __invoke(CompleteUserRegistrationRequest $request): JsonResponse
    {
        $requestPayload = $request->getValidatedPayload();
        $currentUser = $this->userProvider->getAuthenticated();

        $this->userManager->completeRegistration($currentUser, $requestPayload);

        return response()->json(new UserProfileResource($currentUser));
    }
}
