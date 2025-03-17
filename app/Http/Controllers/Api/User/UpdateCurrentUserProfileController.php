<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Http\Requests\Api\User\UpdateCurrentUserRequest;
use App\Http\Resources\Api\UserProfileResource;
use App\Services\User\UserManager;
use App\Services\User\UserProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UpdateCurrentUserProfileController extends Controller
{
    public function __construct(
        private readonly UserProvider $userProvider,
        private readonly UserManager $userManager,
    ) {
    }

    public function __invoke(UpdateCurrentUserRequest $request): JsonResponse
    {
        $authenticatedUser = $this->userProvider->getAuthenticated();
        $updatePayload = $request->getValidatedPayload();

        $this->userManager->updateUser($authenticatedUser, $updatePayload);

        return response()->json(new UserProfileResource($authenticatedUser));
    }
}
