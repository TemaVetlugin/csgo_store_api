<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Entity\DTO\Payload\CompleteUserRegistrationPayload;
use App\Entity\DTO\Payload\UpdateUserPayload;
use App\Models\User;
use App\Repositories\User\AccessTokenRepository;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class UserManager
{
    public function __construct(
        private readonly UserProvider $userProvider,
        private readonly AccessTokenRepository $accessTokenRepository,
        private readonly AuthManager $authManager,
        private readonly SessionManager $sessionManager,
    ) {
    }

    public function completeRegistration(User $user, CompleteUserRegistrationPayload $payload): void
    {
        $user->setSteamTradeLink($payload->getSteamTradeLink());

        $user->setEmail($payload->getEmail());
        $user->setEmailVerifiedAt(null);

        $user->save();

        $user->sendEmailVerificationNotification();
    }

    public function updateUser(User $user, UpdateUserPayload $updatePayload): void
    {
        if ($updatePayload->getSteamTradeLink() !== null) {
            $user->setSteamTradeLink($updatePayload->getSteamTradeLink());
        }

        if ($updatePayload->getEmail() !== null) {
            $user->setEmail($updatePayload->getEmail());
            $user->setEmailVerifiedAt(null);
        }

        $user->save();

        if ($updatePayload->getEmail() !== null) {
                $url = URL::temporarySignedRoute(
                    'verification.verify', // Имя маршрута
                    Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)), // Время действия
                    [
                        'id' => $user->getKey(), // ID пользователя
                        'hash' => sha1($user->getEmailForVerification()), // Хэш email
                    ]
                );
            $user->sendEmailVerificationNotification();
        }
    }

    public function logoutAuthenticatedUser(): void
    {
        $user = $this->userProvider->getAuthenticated();

        $this->authManager->guard('web')->logout();
        $this->sessionManager->flush();

        $this->accessTokenRepository->deleteAllForUser($user);
    }
}
