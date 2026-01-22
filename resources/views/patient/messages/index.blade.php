@extends('layouts.app')

@section('title', 'My Messages - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-stone-900 mb-2">Messages</h1>
        <p class="text-stone-600 mb-0">Communicate with your doctor</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($messages->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <i class="bi bi-chat-dots fs-1 text-stone-300 mb-3"></i>
                <p class="text-stone-500 mb-0">No messages yet.</p>
                <p class="small text-stone-400 mt-2 mb-0">Your doctor will send you messages here.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($messages as $message)
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        @if($message->doctor)
                                            <h6 class="fw-semibold text-stone-900 mb-0">
                                                Dr. {{ $message->doctor->name ?? $message->doctor->email }}
                                            </h6>
                                        @else
                                            <h6 class="fw-semibold text-stone-900 mb-0">You</h6>
                                        @endif
                                        @if($message->sender_type)
                                            <x-badge :variant="$message->sender_type === 'doctor' ? 'primary' : 'info'">
                                                {{ ucfirst($message->sender_type) }}
                                            </x-badge>
                                        @endif
                                        @if($message->is_read)
                                            <x-badge variant="success">Read</x-badge>
                                        @else
                                            <x-badge variant="warning">Unread</x-badge>
                                        @endif
                                    </div>
                                    
                                    <p class="text-stone-700 mb-2" style="white-space: pre-wrap;">{{ $message->message }}</p>
                                    
                                    <div class="d-flex align-items-center gap-3 small text-stone-500">
                                        <span><i class="bi bi-calendar me-1"></i>{{ $message->created_at->format('M d, Y') }}</span>
                                        <span><i class="bi bi-clock me-1"></i>{{ $message->created_at->format('h:i A') }}</span>
                                        @if($message->read_at)
                                            <span><i class="bi bi-check-circle me-1"></i>Read {{ $message->read_at->format('M d, Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
