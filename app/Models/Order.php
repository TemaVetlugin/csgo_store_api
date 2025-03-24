<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Order extends Model
{
    protected $casts = [
        'total_price' => 'double',
        'status' => OrderStatus::class,
    ];

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user_id = $user->getId();

        $this->setRelation('user', $user);

        return $this;
    }

    public function getPayment(): Payment|null
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): static
    {
        $this->payment_id = $payment->getId();

        $this->setRelation('payment', $payment);

        return $this;
    }

    public function getTransactions(): array
    {
        return $this->transactions()->get()->all();
    }

    public function getTotalPrice(): float
    {
        return $this->total_price;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->total_price = $totalPrice;

        return $this;
    }

    public function getTotalPriceCurrency(): string
    {
        return $this->total_price_currency;
    }

    public function setTotalPriceCurrency(string $totalPriceCurrency): static
    {
        $this->total_price_currency = $totalPriceCurrency;

        return $this;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $firstName): static
    {
        $this->first_name = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function setLastName(string $lastName): static
    {
        $this->last_name = $lastName;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCountryIso(): string
    {
        return $this->country_iso;
    }

    public function setCountryIso(string $countryIso): static
    {
        $this->country_iso = $countryIso;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone??'';
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    protected function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    protected function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
