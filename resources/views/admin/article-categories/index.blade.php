@extends('layouts.app')

@section('title', 'Article Categories')

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex justify-content-between">
        <div>
            <h1 class="h2 fw-bold">Article Categories</h1>
        </div>
        <a href="{{ route('admin.article-categories.create') }}" class="btn btn-primary">Add Category</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($categories->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Articles</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td><code>{{ $category->slug }}</code></td>
                                <td>{{ $category->articles_count }}</td>
                                <td>
                                    <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.article-categories.edit', $category) }}" class="btn btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('admin.article-categories.destroy', $category) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card"><div class="card-body text-center py-5"><p class="text-muted mb-0">No categories found</p></div></div>
    @endif
</div>
@endsection
