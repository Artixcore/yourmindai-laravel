@extends('client.layout')

@section('title', 'My Notes - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">My Notes</h4>
    <p class="text-muted mb-0 small">Add text or voice notes for yourself or to share with your doctor</p>
</div>

<!-- Add note: type selector + forms -->
<div class="card mb-3">
    <div class="card-body">
        <ul class="nav nav-tabs nav-fill mb-3" id="noteTypeTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="text-tab" data-bs-toggle="tab" data-bs-target="#text-form" type="button" role="tab">Text note</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="voice-tab" data-bs-toggle="tab" data-bs-target="#voice-form" type="button" role="tab">Voice note</button>
            </li>
        </ul>
        <div class="tab-content" id="noteTypeTabContent">
            <div class="tab-pane fade show active" id="text-form" role="tabpanel">
                <form action="{{ route('client.notes.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="text">
                    <div class="mb-2">
                        <label class="form-label small">Your note</label>
                        <textarea name="content" class="form-control" rows="3" placeholder="Write a note for yourself or for your doctor..." required maxlength="10000"></textarea>
                        @error('content')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Save text note</button>
                </form>
            </div>
            <div class="tab-pane fade" id="voice-form" role="tabpanel">
                <form action="{{ route('client.notes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="voice">
                    <div class="mb-2">
                        <label class="form-label small">Upload or record voice (mp3, wav, ogg, m4a – max 20MB)</label>
                        <input type="file" name="voice" class="form-control form-control-sm" accept="audio/*" required>
                        @error('voice')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Save voice note</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- List of notes -->
<div class="card">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">Recent notes</h6>
    </div>
    <div class="card-body">
        @forelse($notes as $note)
            <div class="d-flex justify-content-between align-items-start border-bottom py-2">
                <div class="flex-grow-1">
                    @if($note->type === 'text')
                        <span class="badge bg-secondary me-1">Text</span>
                        <span>{{ Str::limit($note->content, 120) }}</span>
                    @else
                        <span class="badge bg-info me-1">Voice</span>
                        @if($note->voice_path)
                            <audio controls preload="metadata" class="mt-1" style="max-width:100%; height:36px;">
                                <source src="{{ Storage::url($note->voice_path) }}" type="audio/mpeg">
                                Your browser does not support audio.
                            </audio>
                        @else
                            <span class="text-muted">Voice note</span>
                        @endif
                    @endif
                    <br>
                    <small class="text-muted">{{ $note->created_at->format('M d, Y g:i A') }}</small>
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">No notes yet. Add a text or voice note above.</p>
        @endforelse
    </div>
    @if($notes->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $notes->links() }}
        </div>
    @endif
</div>
@endsection
