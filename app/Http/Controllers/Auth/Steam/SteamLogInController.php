<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Steam;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Ilzrv\LaravelSteamAuth\SteamAuthenticator;

class SteamLogInController extends Controller
{
    public function __construct(
        private readonly SteamAuthenticator $steamAuthenticator,
        private readonly Redirector $redirector,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $steamLogInUrl = $this->steamAuthenticator->buildAuthUrl();
        return $this->redirector->to($steamLogInUrl);
    }
}
