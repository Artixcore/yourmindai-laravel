<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomeworkRequest extends FormRequest
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
            'session_id' => 'nullable|exists:therapy_sessions,id',
            'homework_type' => 'required|in:psychotherapy,lifestyle_modification,sleep_tracking,mood_tracking,personal_journal,risk_tracking,contingency,exercise,parent_role,others_role,self_help_tools',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'goals' => 'nullable|array',
            'frequency' => 'required|in:daily,weekly,as_needed',
            'frequency_type' => 'nullable|in:times_per_day,days_per_week,schedule_rules,as_before',
            'frequency_value' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'reminder_at' => 'nullable|date',
            'requires_parent_feedback' => 'boolean',
            'requires_others_feedback' => 'boolean',
            'media' => 'nullable|array',
            'media.*.type' => 'required_with:media|in:video,audio,podcast,link',
            'media.*.url' => 'required_with:media|url',
            'media.*.title' => 'nullable|string|max:255',
            'contingency_self_action_points' => 'nullable|integer|min:-100|max:100',
            'contingency_others_help_points' => 'nullable|integer|min:-100|max:100',
            'contingency_not_working_points' => 'nullable|integer|min:-100|max:100',
        ];
    }
}
