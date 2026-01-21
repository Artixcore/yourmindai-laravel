@extends('layouts.app')

@section('title', 'My Sessions - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-stone-900 mb-2">Therapy Sessions</h1>
        <p class="text-stone-600 mb-0">View all your therapy sessions</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($sessions->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <i class="bi bi-calendar-x fs-1 text-stone-300 mb-3"></i>
                <p class="text-stone-500 mb-0">No therapy sessions yet.</p>
                <p class="small text-stone-400 mt-2 mb-0">Your doctor will create sessions for you.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($sessions as $session)
                <div class="col-12">
                    <x-patient.session-card :session="$session" />
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
