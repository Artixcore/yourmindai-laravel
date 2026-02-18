<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_name' => 'required|string|max:255',
            'device_type' => 'required|in:mobile,tablet,desktop,wearable,smartwatch,other',
            'device_identifier' => 'nullable|string|max:100',
            'os_type' => 'nullable|string|max:50',
            'os_version' => 'nullable|string|max:50',
            'app_version' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
            'device_source' => 'nullable|in:app_registered,manual,bluetooth',
        ];
    }
}
