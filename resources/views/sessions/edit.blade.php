@extends('layouts.app')

@section('title', 'Edit Session - ' . $session->title)

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-stone-900">Edit Session</h1>
            <p class="text-stone-600 mt-2">{{ $patient->name }}</p>
        </div>
        <a
            href="{{ route('patients.sessions.show', [$patient, $session]) }}"
            class="px-4 py-2 bg-stone-100 text-stone-700 rounded-lg hover:bg-stone-200 transition-colors duration-200 flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back</span>
        </a>
    </div>

    <!-- Form -->
    <x-card>
        <form action="{{ route('patients.sessions.update', [$patient, $session]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-stone-700 mb-2">
                        Session Title <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title', $session->title) }}"
                        required
                        class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('title') border-red-500 @enderror"
                    />
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-stone-700 mb-2">
                        Notes
                    </label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('notes') border-red-500 @enderror"
                    >{{ old('notes', $session->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-stone-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('status') border-red-500 @enderror"
                    >
                        <option value="active" {{ old('status', $session->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="closed" {{ old('status', $session->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-stone-200">
                    <a
                        href="{{ route('patients.sessions.show', [$patient, $session]) }}"
                        class="px-6 py-2 bg-stone-100 text-stone-700 rounded-lg hover:bg-stone-200 transition-colors"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors"
                    >
                        Update Session
                    </button>
                </div>
            </div>
        </form>
    </x-card>
</div>
@endsection
