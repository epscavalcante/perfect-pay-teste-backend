<?php

namespace App\Services\PaymentProcessors\Dtos;

class CreditCardHolderDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $documentNumber,
        public readonly string $postalCode,
        public readonly string $phone,
        public readonly string $addressNumber,
        public readonly ?string $addressComplement = null,
    ) {
    }
}
