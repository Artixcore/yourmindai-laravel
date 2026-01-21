@extends('layouts.app')

@section('title', 'My Medications - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-stone-900 mb-2">My Medications</h1>
        <p class="text-stone-600 mb-0">View your prescribed medications</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($medications->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <i class="bi bi-capsule fs-1 text-stone-300 mb-3"></i>
                <p class="text-stone-500 mb-0">No medications recorded.</p>
                <p class="small text-stone-400 mt-2 mb-0">Your doctor will add medications when prescribed.</p>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Medication</th>
                                <th>Dosage</th>
                                <th>Frequency</th>
                                <th>Start Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medications as $medication)
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-stone-900">
                                            {{ $medication->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-stone-600">
                                            {{ $medication->dosage ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-stone-600">
                                            {{ $medication->frequency ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-stone-600">
                                            {{ isset($medication->start_date) && $medication->start_date ? (is_string($medication->start_date) ? $medication->start_date : $medication->start_date->format('M d, Y')) : 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ ($medication->status ?? 'active') === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($medication->status ?? 'active') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
