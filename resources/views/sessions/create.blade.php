@extends('layouts.app')

@section('title', 'Create Session - ' . $patient->name)

@section('content')
<div class="container-fluid" style="max-width: 768px;">
    <!-- Header -->
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h2 fw-bold text-stone-900">Create Therapy Session</h1>
            <p class="text-stone-600 mt-2 mb-0">{{ $patient->name }}</p>
        </div>
        <a
            href="{{ route('patients.sessions.index', $patient) }}"
            class="btn btn-outline-secondary d-flex align-items-center gap-2"
        >
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back</span>
        </a>
    </div>

    <!-- Form -->
    <x-card>
        <form action="{{ route('patients.sessions.store', $patient) }}" method="POST">
            @csrf

            <div class="d-flex flex-column gap-4">
                <!-- Title -->
                <x-input
                    type="text"
                    id="title"
                    name="title"
                    label="Session Title"
                    value="{{ old('title') }}"
                    required
                    :error="$errors->first('title')"
                    placeholder="e.g., Initial Assessment, Weekly Therapy, etc."
                />

                <!-- Notes -->
                <div class="mb-3">
                    <label for="notes" class="form-label text-stone-700">
                        Notes
                    </label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        class="form-control @error('notes') is-invalid @enderror"
                        placeholder="Optional notes about this session..."
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="status" class="form-label text-stone-700">
                        Status
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="form-select @error('status') is-invalid @enderror"
                    >
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="d-flex align-items-center justify-content-end gap-3 pt-3 border-top border-stone-200">
                    <a
                        href="{{ route('patients.sessions.index', $patient) }}"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Create Session
                    </button>
                </div>
            </div>
        </form>
    </x-card>
</div>
@endsection
