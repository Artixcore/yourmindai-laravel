@extends('layouts.app')

@section('title', 'My Resources - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-stone-900 mb-2">Resources from Doctor</h1>
        <p class="text-stone-600 mb-0">Videos and PDFs shared by your doctor</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($resources->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <i class="bi bi-folder-x fs-1 text-stone-300 mb-3"></i>
                <p class="text-stone-500 mb-0">No resources available yet.</p>
                <p class="small text-stone-400 mt-2 mb-0">Your doctor will share videos and PDFs here.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($resources as $resource)
                <div class="col-12 col-md-6 col-lg-4">
                    <x-patient.resource-card :resource="$resource" />
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
