<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentProcessors\Dtos\CreateCustomerDto;
use App\Services\PaymentProcessors\Dtos\CreatePaymentDto;
use App\Services\PaymentProcessors\Dtos\CreditCardDto;
use App\Services\PaymentProcessors\Dtos\CreditCardHolderDto;
use App\Services\PaymentProcessors\PaymentProcessor;
use Exception;

class PaymentService
{
    public function __construct(
        private readonly PaymentProcessor $paymentProcessor
    ) {
    }

    public function pay(int $orderId, string $paymentMethod, array $creditCard = [], array $creditCardHolder = [], ?string $creditCardToken = null)
    {
        $order = Order::with(['customer'])->findOrFail($orderId);
        $dueDate = date('Y-m-d', strtotime(now().'+ 3 days'));
        $customer = $order->customer;
        $total = $order->total;

        if (! in_array($paymentMethod, ['pix', 'boleto', 'credit_card'])) {
            throw new Exception('Invalid payment method');
        }

        $createCustomerInput = new CreateCustomerDto(
            id: $customer->id,
            name: $customer->name,
            email: $customer->email,
            documentNumber: $customer->document_number
        );
        $customerAsaasId = $this->paymentProcessor->createCustomer($createCustomerInput);

        $paymentTransaction = null;
        $metadata = [];

        if ($paymentMethod === 'pix') {
            $createPaymentDto = new CreatePaymentDto(
                customerId: $customerAsaasId,
                billingType: 'PIX',
                dueDate: $dueDate,
                total: $total
            );

            $paymentTransaction = $this->paymentProcessor->createPayment($createPaymentDto);
            $metadata = [
                'pix' => [
                    'qr_code' => $paymentTransaction['pixQrCode'],
                    'copia_e_cola' => $paymentTransaction['pixCopiaCola'],
                ],
            ];
        }

        if ($paymentMethod === 'boleto') {
            $createPaymentDto = new CreatePaymentDto(
                customerId: $customerAsaasId,
                billingType: 'BOLETO',
                dueDate: $dueDate,
                total: $total
            );
            $paymentTransaction = $this->paymentProcessor->createPayment($createPaymentDto);
            $metadata = [
                'boleto' => [
                    'file_url' => $paymentTransaction['bankSlipUrl'],
                ],
            ];
        }

        if ($paymentMethod === 'credit_card') {
            $creditCardHolderDto = new CreditCardHolderDto(
                name: $creditCardHolder['name'],
                email: $creditCardHolder['email'],
                documentNumber: $creditCardHolder['document_number'],
                postalCode: $creditCardHolder['postalCode'],
                phone: $creditCardHolder['phone'],
                addressNumber: '001',
                addressComplement: null
            );
            $expirationDate = explode('/', $creditCard['expiration_date']);
            $creditCardDto = new CreditCardDto(
                creditCardToken: $creditCardToken,
                holderName: $creditCard['holder_name'],
                number: $creditCard['number'],
                expiryYear: $expirationDate[1],
                expiryMonth: $expirationDate[0],
                cvv: $creditCard['cvv']
            );
            $createPaymentDto = new CreatePaymentDto(
                customerId: $customerAsaasId,
                billingType: 'CREDIT_CARD',
                total: $total,
                dueDate: $dueDate,
                creditCard: $creditCardDto,
                creditCardHolder: $creditCardHolderDto
            );

            $paymentTransaction = $this->paymentProcessor->createPayment($createPaymentDto);
            $metadata = [
                'credit_card' => [
                    'last_digits' => $paymentTransaction['credit_card_brand'],
                    'brand' => $paymentTransaction['credit_card_last_digits'],
                    'token' => $paymentTransaction['credit_card_token'],
                ],
            ];
        }

        $payment = Payment::create([
            'order_id' => $orderId,
            'gateway' => 'asaas',
            'status' => $paymentTransaction['status'],
            'payment_method' => $paymentMethod,
            'gateway_transaction_id' => $paymentTransaction['paymentId'],
            'value' => $total,
            'due_date' => $dueDate,
            'metadata' => json_encode($metadata),
        ]);

        if ($payment->status === 'paid') {
            $order->update(['status' => 'paid']);
        }

        if ($payment->status === 'pending') {
            $order->update(['status' => 'waiting_payment']);
        }
    }
}
