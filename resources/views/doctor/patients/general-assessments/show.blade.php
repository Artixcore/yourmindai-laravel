@extends('layouts.app')
@section('title', 'Assessment - ' . ($assessment->title ?? 'Details'))
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.general-assessments.index', $patient) }}">Assessments</a></li>
                    <li class="breadcrumb-item active">{{ $assessment->title ?? 'Details' }}</li>
                </ol>
            </nav>
            <h2 class="mb-1">{{ $assessment->title ?? 'Assessment Details' }}</h2>
            <p class="text-muted mb-0">Status: {{ $assessment->status ?? '-' }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            @if($assessment->description)<p>{{ $assessment->description }}</p>@endif
            @if($assessment->questions && $assessment->questions->isNotEmpty())
            <h5 class="mt-3">Questions</h5>
            <ul class="list-group">
                @foreach($assessment->questions as $q)
                <li class="list-group-item">{{ $q->question_text ?? '-' }}</li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection
