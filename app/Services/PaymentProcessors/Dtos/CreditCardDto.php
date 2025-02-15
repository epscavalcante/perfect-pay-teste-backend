<?php

namespace App\Services\PaymentProcessors\Dtos;

class CreditCardDto
{
    public function __construct(
        public readonly string $holderName,
        public readonly float $number,
        public readonly string $expiryMonth,
        public readonly string $expiryYear,
        public readonly int $cvv,
        public readonly ?string $creditCardToken = null,
    ) {
    }
}
