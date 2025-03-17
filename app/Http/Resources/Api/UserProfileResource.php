<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /** @var User */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getId(),
            'steam_id' => $this->resource->getSteamId(),
            'name' => $this->resource->getName(),
            'email' => $this->resource->getEmail(),
            'email_verified' => ($this->resource->getEmailVerifiedAt() !== null),
            'avatar_url' => $this->resource->getAvatarUrl(),
            'steam_trade_link' => $this->resource->getSteamTradeLink(),
            'registered_at' => $this->resource->getRegisteredAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
