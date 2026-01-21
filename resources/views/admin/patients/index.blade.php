@extends('layouts.app')

@section('title', 'Admin - Patients')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h2 fw-bold text-stone-900">Patient Management</h1>
            <p class="text-stone-600 mt-2 mb-0">View and manage all patients across the system</p>
        </div>
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

    <!-- Filters -->
    <x-card class="mb-4">
        <form method="GET" action="{{ route('admin.patients.index') }}" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label small text-stone-600">Search</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Name, email, or phone..."
                    class="form-control"
                />
            </div>
            
            <div class="col-12 col-md-3">
                <label class="form-label small text-stone-600">Doctor</label>
                <select name="doctor_id" class="form-select">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->name ?? $doctor->email }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-12 col-md-2">
                <label class="form-label small text-stone-600">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="col-12 col-md-2">
                <label class="form-label small text-stone-600">Created From</label>
                <input
                    type="date"
                    name="created_from"
                    value="{{ request('created_from') }}"
                    class="form-control"
                />
            </div>
            
            <div class="col-12 col-md-2">
                <label class="form-label small text-stone-600">Created To</label>
                <input
                    type="date"
                    name="created_to"
                    value="{{ request('created_to') }}"
                    class="form-control"
                />
            </div>
            
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                @if(request()->hasAny(['search', 'doctor_id', 'status', 'created_from', 'created_to']))
                    <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </div>
        </form>
    </x-card>

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
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Created</th>
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
                                    <div class="text-sm text-stone-600">
                                        {{ $patient->doctor ? ($patient->doctor->name ?? $patient->doctor->email) : '—' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-badge :variant="$patient->status === 'active' ? 'success' : 'default'">
                                        {{ ucfirst($patient->status) }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-stone-600">{{ $patient->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a
                                        href="{{ route('admin.patients.show', $patient) }}"
                                        class="text-teal-600 hover:text-teal-900"
                                        title="View"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
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
            <p class="text-stone-600 mb-0">
                @if(request()->hasAny(['search', 'doctor_id', 'status', 'created_from', 'created_to']))
                    No patients match your filter criteria.
                @else
                    No patients have been created yet.
                @endif
            </p>
        </x-card>
    @endif
</div>
@endsection
