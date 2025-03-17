<?php

declare(strict_types=1);

namespace App\Services\Currency;

use App\Services\Market\Provider\CurrencyProvider;
use App\Services\Utils\MathService;

class CurrencyConverter
{
    public const CURRENCY_PRECISION = 2;
    private const MATH_PRECISION = 8;

    public function __construct(
        private readonly MathService $mathService,
        private readonly CurrencyProvider $currencyProvider,
    ) {
    }

    public function convert(string $amount, string $fromCurrencyCode, string $toCurrencyCode): string
    {
        if ($fromCurrencyCode === $toCurrencyCode) {
            return $amount;
        }

        $conversionRate = $this->getConversionRate($fromCurrencyCode, $toCurrencyCode);

        return $this->mathService->mul($amount, $conversionRate, self::MATH_PRECISION);
    }

    /**
     * "Banking round" - round the lowest currency's fraction (cent's decimals) to the top.
     */
    public function bankingRound(string $amount): string
    {
        $lowestCurrencyFractionsMultiplier = $this->mathService->pow(10, self::CURRENCY_PRECISION);

        // convert operation's amount to a format in currency's lowest fractions
        $amountInLowestFractions = (float) $this->mathService->mul(
            $amount,
            $lowestCurrencyFractionsMultiplier,
            self::CURRENCY_PRECISION
        );

        // round operation's amount in currency's lowest fractions
        $roundedAmountInLowestFractions = (string) ceil($amountInLowestFractions);

        // convert back operation's amount from the lowest fractions to standard
        $roundedAmount = $this->mathService->div(
            $roundedAmountInLowestFractions,
            $lowestCurrencyFractionsMultiplier,
            self::CURRENCY_PRECISION
        );

        return number_format((float) $roundedAmount, self::CURRENCY_PRECISION, '.', '');
    }

    private function getConversionRate(string $fromCurrencyCode, string $toCurrencyCode): string
    {
        $fromCurrency = $this->currencyProvider->getByCode($fromCurrencyCode);
        $toCurrency = $this->currencyProvider->getByCode($toCurrencyCode);

        return $this->mathService->div((string) $toCurrency['rate'], (string) $fromCurrency['rate'], self::MATH_PRECISION);
    }
}
