<?php

namespace App\Http\Requests;

use App\Models\Task;
use App\Models\ParentLink;
use Illuminate\Foundation\Http\FormRequest;

class VerifyTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Parent can only verify tasks for their linked children.
     */
    public function authorize(): bool
    {
        $task = $this->route('task');
        if (!$task instanceof Task) {
            return false;
        }

        return ParentLink::where('parent_id', $this->user()->id)
            ->where('patient_id', $task->patient_id)
            ->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'verified' => 'sometimes|boolean',
        ];
    }
}
