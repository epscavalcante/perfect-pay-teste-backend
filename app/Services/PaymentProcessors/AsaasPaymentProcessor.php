<?php

namespace App\Services\PaymentProcessors;

use App\Services\PaymentProcessors\Dtos\CreateCustomerDto;
use App\Services\PaymentProcessors\Dtos\CreatePaymentDto;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasPaymentProcessor implements PaymentProcessor
{
    public function createCustomer(CreateCustomerDto $data): string|int
    {
        $response = $this->postJson(
            path: 'customers',
            data: [
                'name' => $data->name,
                'cpfCnpj' => $data->documentNumber,
                'email' => $data->email,
                'mobilePhone' => $data->phoneNumber,
                'externalReference' => $data->id,
                'notificationDisabled' => true,
            ]
        );

        return $response->json()['id'];
    }

    public function createPayment(CreatePaymentDto $data): array
    {
        $body = [
            'customer' => $data->customerId,
            'billingType' => $data->billingType,
            'value' => $data->total,
            'dueDate' => $data->dueDate,
        ];

        if ($data->creditCard) {

            if ($data->creditCard->creditCardToken) {
                $body['creditCardToken'] = $data->creditCard->creditCardToken;
            } else {
                $body['creditCard'] = [
                    'holderName' => $data->creditCard->holderName,
                    'number' => $data->creditCard->number,
                    'expiryMonth' => $data->creditCard->expiryMonth,
                    'expiryYear' => $data->creditCard->expiryYear,
                    'ccv' => $data->creditCard->cvv,
                ];
                $body['creditCardHolderInfo'] = [
                    'name' => $data->creditCardHolder->name,
                    'email' => $data->creditCardHolder->email,
                    'cpfCnpj' => $data->creditCardHolder->documentNumber,
                    'postalCode' => $data->creditCardHolder->postalCode,
                    'phone' => $data->creditCardHolder->phone,
                    'mobilePhone' => $data->creditCardHolder->phone,
                    'addressNumber' => $data->creditCardHolder->addressNumber,
                    'addressComplement' => $data->creditCardHolder->addressComplement,
                ];
            }
        }

        $response = $this->postJson(
            path: 'payments',
            data: $body
        );

        $asaasStatus = [
            'CONFIRMED' => 'paid',
            'PENDING' => 'pending',
        ];
        $output = [
            'paymentId' => $response->json('id'),
            'pixQrCode' => null,
            'pixCopiaCola' => null,
            'bankSlipUrl' => $response->json('bankSlipUrl', null),
            'credit_card_brand' => null,
            'credit_card_token' => null,
            'credit_card_last_digits' => null,
            'status' => $asaasStatus[$response->json('status')],
        ];

        if (strtolower($data->billingType) === 'pix') {
            $getPixResponse = $this->getPixQrCode($response->json('id'));
            $output['pixQrCode'] = $getPixResponse['encodedImage'];
            $output['pixCopiaCola'] = $getPixResponse['payload'];
        }

        if (strtolower($data->billingType) === 'credit_card') {
            $output['credit_card_brand'] = $response->json('creditCard.creditCardBrand');
            $output['credit_card_token'] = $response->json('creditCard.creditCardToken');
            $output['credit_card_last_digits'] = $response->json('creditCard.creditCardNumber');
        }

        return $output;
    }

    private function getPixQrCode(string $paymentId)
    {
        $response = $this->getJson("payments/{$paymentId}/pixQrCode");
        $output = [
            'encodedImage' => $response->json('encodedImage'),
            'payload' => $response->json('payload'),
        ];

        return $output;
    }

    private function postJson(string $path, array $data = [], array $headers = [])
    {
        $_headers = $this->getHeaders($headers);
        $_url = $this->getUrl($path);
        $response = Http::withHeaders($_headers)->post($_url, $data);

        if (! $response->successful()) {
            $errorMessage = 'Erro ao processar pagamento';
            Log::error($errorMessage, $response->json());
            throw new Exception($errorMessage);
        }

        return $response;
    }

    private function getJson(string $path, array $headers = [])
    {
        $_headers = $this->getHeaders($headers);
        $_url = $this->getUrl($path);

        return Http::withHeaders($_headers)->get($_url);
    }

    private function getUrl(string $path): string
    {
        $baseUrl = config('services.asaas.api_base_url');
        $version = config('services.asaas.api_version');

        return $baseUrl.'/'.$version.'/'.$path;
    }

    private function getHeaders(array $headers = []): array
    {
        $defaultHeaders = [
            'content-type' => 'application/json',
            'User-Agent' => config('app.name'),
            'accept' => 'application/json',
            'access_token' => config('services.asaas.api_token'),
        ];

        $headers = array_merge(
            $defaultHeaders,
            $headers
        );

        return $headers;
    }
}
