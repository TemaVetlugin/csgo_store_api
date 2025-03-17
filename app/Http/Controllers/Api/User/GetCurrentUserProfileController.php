<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Http\Resources\Api\UserProfileResource;
use App\Services\User\UserProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class GetCurrentUserProfileController extends Controller
{
    public function __construct(private readonly UserProvider $userProvider)
    {
    }

    public function __invoke(): JsonResponse
    {
        $userProfileApiResource = new UserProfileResource($this->userProvider->getAuthenticated());

        return response()->json($userProfileApiResource);
    }
}
