@php
$unreadCount = auth()->user()->unreadNotifications()->count();
@endphp
<div class="dropdown" id="notificationDropdown">
    <button class="btn btn-link position-relative p-2 text-white border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
        <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $unreadCount > 0 ? '' : 'd-none' }}" style="font-size: 0.5rem; padding: 0.125rem 0.25rem; min-width: 1rem;" data-count="{{ $unreadCount }}">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm border notification-list" style="min-width: 280px; max-height: 400px; overflow-y: auto;">
        <li class="px-3 py-2 d-flex justify-content-between align-items-center">
            <span class="small text-muted fw-semibold">Notifications</span>
            @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link btn-sm p-0 text-primary">Mark all read</button>
            </form>
            @endif
        </li>
        <li><hr class="dropdown-divider my-0"></li>
        @forelse(auth()->user()->unreadNotifications()->limit(10)->get() as $notification)
        @php
        $data = $notification->data;
        $title = $data['title'] ?? 'Notification';
        $message = $data['message'] ?? '';
        $url = $data['url'] ?? '#';
        @endphp
        <li>
            <a href="{{ $url }}" class="dropdown-item py-2 notification-item" data-id="{{ $notification->id }}">
                <div class="fw-semibold small text-dark">{{ $title }}</div>
                <div class="small text-muted">{{ Str::limit($message, 60) }}</div>
                <div class="small text-muted mt-1">{{ $notification->created_at->diffForHumans() }}</div>
            </a>
        </li>
        @empty
        <li class="px-3 py-2 small text-muted">No new notifications</li>
        @endforelse
    </ul>
</div>
