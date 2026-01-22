@extends('layouts.app')

@section('title', 'Messages - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-stone-900 mb-2">Messages</h1>
        <p class="text-stone-600 mb-0">Communicate with your patients</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <x-card class="mb-4">
        <form method="GET" action="{{ route('doctors.messages.index') }}" class="d-flex flex-wrap align-items-end gap-3">
            <div class="flex-grow-1" style="min-width: 200px;">
                <label class="form-label small text-stone-700 mb-1">Patient</label>
                <select name="patient_id" class="form-select form-select-sm">
                    <option value="">All Patients</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ $filterPatientId == $patient->id ? 'selected' : '' }}>
                            {{ $patient->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 150px;">
                <label class="form-label small text-stone-700 mb-1">Status</label>
                <select name="is_read" class="form-select form-select-sm">
                    <option value="">All Messages</option>
                    <option value="0" {{ $filterIsRead === '0' ? 'selected' : '' }}>Unread</option>
                    <option value="1" {{ $filterIsRead === '1' ? 'selected' : '' }}>Read</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('doctors.messages.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </x-card>

    @if($messages->isEmpty())
        <x-card>
            <div class="text-center py-5">
                <i class="bi bi-chat-dots fs-1 text-stone-300 mb-3"></i>
                <p class="text-stone-500 mb-0">No messages yet.</p>
            </div>
        </x-card>
    @else
        <!-- Messages grouped by patient -->
        @foreach($messagesByPatient as $patientId => $patientMessages)
            @php
                $patient = $patients[$patientId] ?? null;
                $unreadCount = $unreadCounts[$patientId] ?? 0;
            @endphp
            
            <x-card class="mb-3">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <h5 class="fw-semibold text-stone-900 mb-1">
                            @if($patient)
                                <a href="{{ route('patients.show', $patient) }}" class="text-decoration-none">
                                    {{ $patient->name }}
                                </a>
                            @else
                                Patient #{{ $patientId }}
                            @endif
                        </h5>
                        @if($unreadCount > 0)
                            <span class="badge bg-warning text-dark">{{ $unreadCount }} unread</span>
                        @endif
                    </div>
                </div>
                
                <div class="border-top pt-3">
                    @foreach($patientMessages->take(5) as $message)
                        <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    @if($message->sender_type === 'doctor')
                                        <span class="badge bg-primary">You</span>
                                    @else
                                        <span class="badge bg-info">Patient</span>
                                    @endif
                                    @if($message->is_read)
                                        <span class="badge bg-success">Read</span>
                                    @else
                                        <span class="badge bg-warning">Unread</span>
                                    @endif
                                </div>
                                <small class="text-stone-500">
                                    {{ $message->created_at->format('M d, Y h:i A') }}
                                </small>
                            </div>
                            <p class="text-stone-700 mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>
                        </div>
                    @endforeach
                    
                    @if($patientMessages->count() > 5)
                        <p class="text-muted small mb-0">
                            <i class="bi bi-three-dots"></i> {{ $patientMessages->count() - 5 }} more message(s)
                        </p>
                    @endif
                </div>
            </x-card>
        @endforeach
    @endif
</div>
@endsection
