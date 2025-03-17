<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\Steam\SteamCallbackController;
use App\Services\User\UserManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cookie;

class LogoutController extends Controller
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly Redirector $redirector,
        private readonly string $homePageUrl,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $this->userManager->logoutAuthenticatedUser();

        return $this->redirector->to($this->homePageUrl)
            ->withCookie(Cookie::forget(SteamCallbackController::COOKIE_AUTH_TOKEN))
        ;
    }
}
