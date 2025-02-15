<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
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
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|int|min:1',
            'customer' => 'required|array',
            'customer.name' => 'required|min:3|max:200',
            'customer.email' => 'required|email',
            'customer.document_number' => 'required|size:11'
        ];
    }
}
