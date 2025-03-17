<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'steam_id',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSteamId(): string
    {
        return $this->steam_id;
    }

    public function getEmail(): string|null
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEmailVerifiedAt(): Carbon|null
    {
        return $this->email_verified_at;
    }

    public function setEmailVerifiedAt(Carbon|null $emailVerifiedAt): static
    {
        $this->email_verified_at = $emailVerifiedAt;

        return $this;
    }

    public function getSteamTradeLink(): string|null
    {
        return $this->steam_trade_link;
    }

    public function setSteamTradeLink(string $steamTradeLink): static
    {
        $this->steam_trade_link = $steamTradeLink;

        return $this;
    }

    public function getAvatarUrl(): string|null
    {
        return $this->avatar_url;
    }

    public function getRegisteredAt(): Carbon|null
    {
        return $this->{$this->getCreatedAtColumn()};
    }
}
