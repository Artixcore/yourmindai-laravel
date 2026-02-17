<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSleepHygieneLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sleep_hygiene_item_id' => 'required|exists:sleep_hygiene_items,id',
            'log_date' => 'required|date',
            'is_completed' => 'required|boolean',
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
