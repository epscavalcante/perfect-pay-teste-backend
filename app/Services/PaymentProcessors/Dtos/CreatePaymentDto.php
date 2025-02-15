<?php

namespace App\Services\PaymentProcessors\Dtos;

class CreatePaymentDto
{
    public function __construct(
        public readonly string $customerId,
        public readonly string $billingType,
        public readonly float $total,
        public readonly string $dueDate,
        public readonly ?CreditCardDto $creditCard = null,
        public readonly ?CreditCardHolderDto $creditCardHolder = null,
    ) {
    }
}
