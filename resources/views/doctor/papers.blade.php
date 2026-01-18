@extends('layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Doctor Papers')

@section('content')
<div class="container-fluid" x-data="papersPage()">
    <!-- Header -->
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h2 fw-bold text-stone-900">Documents & Papers</h1>
            <p class="text-stone-600 mt-2 mb-0">Manage your professional documents, licenses, and certificates</p>
        </div>
        <button
            @click="$dispatch('paper-upload-open')"
            class="btn btn-primary d-flex align-items-center gap-2"
        >
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Upload Document</span>
        </button>
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

    <!-- Papers Grid -->
    @if($papers->count() > 0)
        <div class="row g-4">
            @foreach($papers as $paper)
                <div class="col-12 col-md-6 col-lg-4">
                    <x-card 
                        class="h-100"
                        x-data="{ showActions: false }"
                    >
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <!-- File Type Icon -->
                                @if($paper->is_pdf)
                                    <div class="bg-red-100 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <svg style="width: 24px; height: 24px;" class="text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="bg-primary bg-opacity-25 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <svg style="width: 24px; height: 24px;" class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <h3 class="h6 font-semibold text-stone-900 text-truncate mb-1">{{ $paper->title }}</h3>
                                    <div class="d-flex align-items-center gap-2">
                                        <x-badge :variant="$paper->category === 'license' ? 'success' : ($paper->category === 'certificate' ? 'info' : 'default')">
                                            {{ ucfirst($paper->category) }}
                                        </x-badge>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions Menu -->
                            <div class="dropdown" @click.stop>
                                <button
                                    @click="showActions = !showActions"
                                    class="btn btn-link p-2 text-stone-400 hover-text-stone-600 rounded"
                                >
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>
                                
                                <div
                                    x-show="showActions"
                                    x-cloak
                                    @click.away="showActions = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="dropdown-menu dropdown-menu-end shadow border border-stone-200"
                                    style="display: none; min-width: 192px;"
                                >
                                    <a href="{{ route('doctors.papers.download', $paper) }}" class="dropdown-item text-stone-700">Download</a>
                                    <a href="{{ route('doctors.papers.edit', $paper) }}" class="dropdown-item text-stone-700">Edit</a>
                                    <hr class="dropdown-divider">
                                    <form action="{{ route('doctors.papers.destroy', $paper) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger w-100 text-start border-0 bg-transparent">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Details -->
                        <div class="d-flex flex-column gap-2 small text-stone-600">
                            @if($paper->issued_date)
                                <div class="d-flex align-items-center gap-2">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Issued: {{ $paper->issued_date->format('M d, Y') }}</span>
                                </div>
                            @endif
                            <div class="d-flex align-items-center gap-2">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Uploaded: {{ $paper->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        
                        @if($paper->notes)
                            <div class="mt-3 pt-3 border-top border-stone-200">
                                <p class="small text-stone-600 mb-0">{{ Str::limit($paper->notes, 100) }}</p>
                            </div>
                        @endif
                    </x-card>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <x-card class="text-center py-5">
            <svg class="mx-auto mb-3 text-stone-400" style="width: 64px; height: 64px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="h5 font-semibold text-stone-900 mb-2">No documents uploaded</h3>
            <p class="text-stone-600 mb-4">Get started by uploading your first document</p>
            <button
                @click="$dispatch('paper-upload-open')"
                class="btn btn-primary d-inline-flex align-items-center gap-2"
            >
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Upload Document</span>
            </button>
        </x-card>
    @endif

    <!-- Upload Modal -->
    <x-paper-upload-modal :doctorId="$doctor->id ?? null" />
</div>

<script>
function papersPage() {
    return {
        init() {
            // Listen for paper upload events
            window.addEventListener('paper-upload-open', () => {
                // Modal will handle this
            });
        }
    }
}
</script>
@endsection
