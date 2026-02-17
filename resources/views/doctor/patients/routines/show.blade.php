@extends('layouts.app')
@section('title', 'Routine Details')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patientProfile->user->name ?? 'Patient' }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.routines.index', $patient) }}">Routines</a></li>
                    <li class="breadcrumb-item active">{{ $routine->title ?? 'Details' }}</li>
                </ol>
            </nav>
            <h2 class="mb-1">{{ $routine->title ?? 'Routine Details' }}</h2>
        </div>
    </div>
    <div class="card"><div class="card-body">
        <p><strong>Frequency:</strong> {{ $routine->frequency ?? '-' }}</p>
        <p><strong>Status:</strong> {{ $routine->is_active ? 'Active' : 'Inactive' }}</p>
        @if($routine->items && $routine->items->isNotEmpty())
        <h5 class="mt-3">Items</h5>
        <ul class="list-group">
            @foreach($routine->items as $item)
            <li class="list-group-item">{{ $item->title ?? '-' }}</li>
            @endforeach
        </ul>
        @endif
    </div></div>
</div>
@endsection
