<?php

namespace App\Services\PaymentProcessors;

use App\Services\PaymentProcessors\Dtos\CreateCustomerDto;
use App\Services\PaymentProcessors\Dtos\CreatePaymentDto;

interface PaymentProcessor
{
    public function createCustomer(CreateCustomerDto $data): string|int;

    public function createPayment(CreatePaymentDto $data): array;
}
