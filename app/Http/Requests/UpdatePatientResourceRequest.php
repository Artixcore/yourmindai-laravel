<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $resource = $this->route('resource');
        $user = $this->user();
        
        return $user->role === 'admin' || $resource->doctor_id === $user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $patientId = $this->route('patient')?->id ?? $this->input('patient_id');
        $resource = $this->route('resource');
        $currentType = $resource ? $resource->type : $this->input('type');
        
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'session_id' => [
                'nullable',
                'exists:therapy_sessions,id',
                Rule::exists('therapy_sessions', 'id')->where('patient_id', $patientId),
            ],
            'session_day_id' => [
                'nullable',
                'exists:session_days,id',
                function ($attribute, $value, $fail) {
                    $sessionId = $this->input('session_id');
                    if ($value && $sessionId) {
                        $sessionDay = \App\Models\SessionDay::where('id', $value)
                            ->where('session_id', $sessionId)
                            ->first();
                        if (!$sessionDay) {
                            $fail('The selected session day does not belong to the selected session.');
                        }
                    }
                },
            ],
            'type' => ['required', 'in:pdf,youtube'],
            'title' => ['required', 'string', 'max:255'],
            'file' => [
                function ($attribute, $value, $fail) use ($currentType) {
                    $newType = $this->input('type', $currentType);
                    // If changing to PDF or already PDF and no file exists, require file
                    if ($newType === 'pdf' && !$this->hasFile('file') && !$this->route('resource')?->file_path) {
                        $fail('A PDF file is required when type is PDF.');
                    }
                },
                'nullable',
                'file',
                'mimes:pdf',
                'max:10240',
            ],
            'youtube_url' => [
                function ($attribute, $value, $fail) use ($currentType) {
                    $newType = $this->input('type', $currentType);
                    // If changing to YouTube or already YouTube and no URL exists, require URL
                    if ($newType === 'youtube' && !$value && !$this->route('resource')?->youtube_url) {
                        $fail('A YouTube URL is required when type is YouTube.');
                    }
                },
                'nullable',
                'url',
                function ($attribute, $value, $fail) {
                    if ($value && !$this->isValidYouTubeUrl($value)) {
                        $fail('The youtube URL must be a valid YouTube video URL.');
                    }
                },
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');
            $file = $this->hasFile('file');
            $youtubeUrl = $this->input('youtube_url');
            $resource = $this->route('resource');

            // If type is PDF, require file (if not already exists) and forbid youtube_url
            if ($type === 'pdf') {
                if (!$file && !$resource?->file_path) {
                    $validator->errors()->add('file', 'A PDF file is required when type is PDF.');
                }
                if ($youtubeUrl) {
                    $validator->errors()->add('youtube_url', 'YouTube URL is not allowed when type is PDF.');
                }
            }

            // If type is YouTube, require youtube_url (if not already exists) and forbid file
            if ($type === 'youtube') {
                if (!$youtubeUrl && !$resource?->youtube_url) {
                    $validator->errors()->add('youtube_url', 'A YouTube URL is required when type is YouTube.');
                }
                if ($file) {
                    $validator->errors()->add('file', 'File upload is not allowed when type is YouTube.');
                }
            }
        });
    }

    /**
     * Check if URL is a valid YouTube URL.
     */
    private function isValidYouTubeUrl(string $url): bool
    {
        $patterns = [
            '/^https?:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/)([a-zA-Z0-9_-]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }
}
