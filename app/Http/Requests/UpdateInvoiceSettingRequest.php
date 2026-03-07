<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
            'footer_text' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
