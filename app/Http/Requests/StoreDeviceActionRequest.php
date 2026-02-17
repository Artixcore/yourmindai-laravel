<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action_type' => 'required|in:connected_smartwatch,logged_screentime,tracked_sleep,synced_steps,manual_entry,other',
            'device_id' => 'nullable|exists:patient_devices,id',
            'action_note' => 'nullable|string|max:2000',
        ];
    }
}
