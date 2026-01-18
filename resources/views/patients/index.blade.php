@extends('layouts.app')

@section('title', 'Patients')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h2 fw-bold text-stone-900">Patients</h1>
            <p class="text-stone-600 mt-2 mb-0">Manage your patient accounts</p>
        </div>
        <a
            href="{{ route('patients.create') }}"
            class="btn btn-primary d-flex align-items-center gap-2"
        >
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Create Patient</span>
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="alert alert-success alert-dismissible fade show mb-4"
            x-init="setTimeout(() => show = false, 5000)"
        >
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-4">
        <form method="GET" action="{{ route('patients.index') }}" class="d-flex align-items-center gap-3">
            <div class="flex-grow-1">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by name or email..."
                    class="form-control"
                />
            </div>
            <button
                type="submit"
                class="btn btn-secondary"
            >
                Search
            </button>
            @if($search)
                <a
                    href="{{ route('patients.index') }}"
                    class="btn btn-outline-secondary"
                >
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Patients Table -->
    @if($patients->count() > 0)
        <x-card class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-stone-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Photo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-stone-200">
                        @foreach($patients as $patient)
                            <tr class="hover-bg-stone-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($patient->photo_path)
                                        <img 
                                            src="{{ $patient->photo_url }}" 
                                            alt="{{ $patient->name }}"
                                            class="w-10 h-10 rounded-full object-cover"
                                        />
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center">
                                            <span class="text-teal-600 font-semibold text-sm">
                                                {{ strtoupper(substr($patient->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-stone-900">{{ $patient->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-stone-600">{{ $patient->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-stone-600">{{ $patient->phone ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-badge :variant="$patient->status === 'active' ? 'success' : 'default'">
                                        {{ ucfirst($patient->status) }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a
                                            href="{{ route('patients.show', $patient) }}"
                                            class="text-teal-600 hover:text-teal-900"
                                            title="View"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a
                                            href="{{ route('patients.edit', $patient) }}"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Edit"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form
                                            action="{{ route('patients.destroy', $patient) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Are you sure you want to deactivate this patient?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="text-red-600 hover:text-red-900"
                                                title="Deactivate"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($patients->hasPages())
                <div class="p-3 border-top border-stone-200">
                    {{ $patients->links() }}
                </div>
            @endif
        </x-card>
    @else
        <!-- Empty State -->
        <x-card class="text-center py-5">
            <svg class="mx-auto mb-3 text-stone-400" style="width: 64px; height: 64px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="h5 fw-semibold text-stone-900 mb-2">No patients found</h3>
            <p class="text-stone-600 mb-4">
                @if($search)
                    No patients match your search criteria.
                @else
                    Get started by creating your first patient account.
                @endif
            </p>
            <a
                href="{{ route('patients.create') }}"
                class="btn btn-primary d-inline-flex align-items-center gap-2"
            >
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Create Patient</span>
            </a>
        </x-card>
    @endif
</div>
@endsection
