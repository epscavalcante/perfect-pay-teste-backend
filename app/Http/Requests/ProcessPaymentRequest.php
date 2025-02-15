<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:pix,boleto,credit_card',
            'credit_card' => 'exclude_unless:payment_method,credit_card|required_if:payment_method,credit_card|array',
            'credit_card.holder_name' => 'exclude_unless:payment_method,credit_card|required_if:payment_method,credit_card|min:3|max:200',
            'credit_card.number' => 'exclude_unless:payment_method,credit_card|required_if:payment_method,credit_card|size:16',
            'credit_card.expiration_date' => [
                'exclude_unless:payment_method,credit_card|required_if:payment_method,credit_card',
                'regex:/^(0[1-9]|1[0-2])\/?([0-9]{2})$/',
            ],
            'credit_card.cvv' => 'exclude_unless:payment_method,credit_card|required_if:payment_method,credit_card|size:3',
            'holder' => 'exclude_unless:payment_method,credit_card|required_if:payment_method,credit_card|array',
            'holder.name' => 'exclude_unless:payment_method,credit_card|required_if:payment_method,credit_card|min:3|max:200',
            'holder.document_number' => 'exclude_unless:payment_method,credit_card|required_if:payment_method,credit_card|size:11',
            'holder.email' => 'exclude_unless:payment_method,credit_card|required_if:payment_method,credit_card|email',
            'holder.phone' => 'exclude_unless:payment_method,credit_card|required_if:payment_method,credit_card|size:11',
            'holder.postalCode' => 'exclude_unless:payment_method,credit_card|required_if:payment_method,credit_card|size:8',
        ];
    }
}
