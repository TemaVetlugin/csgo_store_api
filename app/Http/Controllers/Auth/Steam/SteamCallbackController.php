<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Steam;

use App\Exceptions\Steam\SteamAuthenticationException;
use App\Repositories\SteamUserRepository;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Ilzrv\LaravelSteamAuth\Exceptions\Authentication\SteamIdNotFoundAuthenticationException;
use Ilzrv\LaravelSteamAuth\Exceptions\Authentication\SteamResponseNotValidAuthenticationException;
use Ilzrv\LaravelSteamAuth\Exceptions\Validation\ValidationException;
use Ilzrv\LaravelSteamAuth\SteamAuthenticator;
use Ilzrv\LaravelSteamAuth\SteamUserDto;
use Symfony\Component\HttpFoundation\Cookie;

class SteamCallbackController extends Controller
{
    public const COOKIE_AUTH_TOKEN = 'AUTH-TOKEN';

    public function __construct(
        private readonly SteamAuthenticator $steamAuthenticator,
        private readonly SteamUserRepository $steamUserRepository,
        private readonly AuthManager $authManager,
        private readonly Redirector $redirector,
        private readonly string $homePageUrl,
    ) {
    }

    /**
     * @throws SteamIdNotFoundAuthenticationException
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $steamUser = $this->getSteamUser();

        $user = $this->steamUserRepository->firstOrCreate($steamUser);

        $this->authManager->login($user, true);

        $authToken = $user->createToken('auth_token');

        return $this->redirector->to($this->homePageUrl)
            ->withCookie(new Cookie(self::COOKIE_AUTH_TOKEN, $authToken->plainTextToken))
        ;
    }

    private function getSteamUser(): SteamUserDto
    {
        try {
            $this->steamAuthenticator->auth();
        } catch (ValidationException | SteamResponseNotValidAuthenticationException $e) {
throw new SteamAuthenticationException($e->getMessage());
//            throw new SteamAuthenticationException('An invalid response received from Steam.');
        }

        $steamUser = $this->steamAuthenticator->getSteamUser();
        if ($steamUser === null) {
            throw new SteamIdNotFoundAuthenticationException('User cannot be resolved from Steam response.');
        }

        return $steamUser;
    }
}
