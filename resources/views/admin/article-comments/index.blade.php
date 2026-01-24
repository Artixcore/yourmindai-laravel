@extends('layouts.app')

@section('title', 'Article Comments')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h2 fw-bold">Article Comments</h1>
        <p class="text-muted">Moderate user comments</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small>Total</small><h4>{{ $stats['total'] }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small>Pending</small><h4 class="text-warning">{{ $stats['pending'] }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small>Approved</small><h4 class="text-success">{{ $stats['approved'] }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small>Rejected</small><h4 class="text-danger">{{ $stats['rejected'] }}</h4></div></div></div>
    </div>

    @if ($comments->count() > 0)
        <div class="card border-0 shadow-sm">
            @foreach ($comments as $comment)
                <div class="card-body border-bottom">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>{{ $comment->commenter_name }}</h6>
                            <p class="mb-2">{{ $comment->comment }}</p>
                            <small class="text-muted">
                                On: <a href="{{ route('articles.public.show', $comment->article->slug) }}">{{ $comment->article->title }}</a> • 
                                {{ $comment->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div class="btn-group btn-group-sm">
                            @if ($comment->status !== 'approved')
                                <form method="POST" action="{{ route('admin.article-comments.approve', $comment) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Approve</button>
                                </form>
                            @endif
                            @if ($comment->status !== 'rejected')
                                <form method="POST" action="{{ route('admin.article-comments.reject', $comment) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $comments->links() }}</div>
    @else
        <div class="card"><div class="card-body text-center py-5"><p class="text-muted mb-0">No comments found</p></div></div>
    @endif
</div>
@endsection
