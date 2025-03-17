<?php

declare(strict_types=1);

namespace App\Services\Utils;

class MathService
{
    public function mul(int|float|string $left, int|float|string $right, int $scale = null): string
    {
        return bcmul((string) $left, (string) $right, $scale);
    }

    public function div(int|float|string $left, int|float|string $right, int $scale = null): string
    {
        return bcdiv($left, $right, $scale);
    }

    public function pow(int|float|string $base, int $exponent): string
    {
        return bcpow((string) $base, (string) $exponent);
    }
}
