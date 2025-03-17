<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Ilzrv\LaravelSteamAuth\SteamUserDto;

class SteamUserRepository
{
    public function firstOrCreate(SteamUserDto $steamUserDto): User
    {
        return User::firstOrCreate([
            'steam_id' => $steamUserDto->getSteamId(),
        ], [
            'name' => $steamUserDto->getPersonaName() ?? 'User',
            'avatar_url' => $steamUserDto->getAvatarFull(),
            'email_verified_at' => Carbon::now(),
        ]);
    }
}
