@extends('layouts.app')

@section('title', 'Doctor Papers')

@section('content')
<div class="max-w-7xl mx-auto" x-data="papersPage()">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-stone-900">Documents & Papers</h1>
            <p class="text-stone-600 mt-2">Manage your professional documents, licenses, and certificates</p>
        </div>
        <button
            @click="$dispatch('paper-upload-open')"
            class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors duration-200 flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            class="mb-6 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-lg"
            x-init="setTimeout(() => show = false, 5000)"
        >
            {{ session('success') }}
        </div>
    @endif

    <!-- Papers Grid -->
    @if($papers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($papers as $paper)
                <x-card 
                    class="transform transition-all duration-200 hover:scale-105"
                    x-data="{ showActions: false }"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <!-- File Type Icon -->
                            @if($paper->is_pdf)
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-stone-900 truncate">{{ $paper->title }}</h3>
                                <div class="flex items-center space-x-2 mt-1">
                                    <x-badge :type="$paper->category === 'license' ? 'success' : ($paper->category === 'certificate' ? 'info' : 'default')">
                                        {{ ucfirst($paper->category) }}
                                    </x-badge>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions Menu -->
                        <div class="relative" @click.stop>
                            <button
                                @click="showActions = !showActions"
                                class="p-2 text-stone-400 hover:text-stone-600 rounded-lg hover:bg-stone-100 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-stone-200 py-1 z-10"
                            >
                                <a href="{{ route('doctors.papers.download', $paper) }}" class="block px-4 py-2 text-sm text-stone-700 hover:bg-stone-50">
                                    Download
                                </a>
                                <a href="{{ route('doctors.papers.edit', $paper) }}" class="block px-4 py-2 text-sm text-stone-700 hover:bg-stone-50">
                                    Edit
                                </a>
                                <form action="{{ route('doctors.papers.destroy', $paper) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-stone-50">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Details -->
                    <div class="space-y-2 text-sm text-stone-600">
                        @if($paper->issued_date)
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Issued: {{ $paper->issued_date->format('M d, Y') }}</span>
                            </div>
                        @endif
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Uploaded: {{ $paper->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    
                    @if($paper->notes)
                        <div class="mt-4 pt-4 border-t border-stone-200">
                            <p class="text-sm text-stone-600">{{ Str::limit($paper->notes, 100) }}</p>
                        </div>
                    @endif
                </x-card>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <x-card class="text-center py-12">
            <svg class="mx-auto h-16 w-16 text-stone-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-lg font-semibold text-stone-900 mb-2">No documents uploaded</h3>
            <p class="text-stone-600 mb-6">Get started by uploading your first document</p>
            <button
                @click="$dispatch('paper-upload-open')"
                class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors duration-200 inline-flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
