<?php

namespace App\Services\PaymentProcessors\Dtos;

class CreateCustomerDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $documentNumber,
        public readonly ?string $phoneNumber = null,
    ) {
    }
}
