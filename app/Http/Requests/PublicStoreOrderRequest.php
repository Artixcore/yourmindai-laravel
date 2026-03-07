<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicStoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cities = config('bangladesh_cities.cities', []);

        return [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_address' => 'required|string|max:1000',
            'customer_city' => ['required', 'string', Rule::in($cities)],
            'customer_email' => 'nullable|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Please enter your name.',
            'customer_phone.required' => 'Please enter your phone number.',
            'customer_address.required' => 'Please enter your address.',
            'customer_city.required' => 'Please select your city.',
            'customer_city.in' => 'Please select a valid city from the list.',
        ];
    }
}
