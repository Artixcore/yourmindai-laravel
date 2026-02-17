@props(['user', 'showSearch' => false])

<div class="topbar position-fixed top-0 start-0 end-0" style="z-index: 1050;">
    <div class="d-flex align-items-center justify-content-between px-4 px-md-5 h-100">
        <div class="d-flex align-items-center gap-3">
            <!-- Mobile Menu Toggle -->
            <button class="btn btn-link d-md-none text-muted p-2 border-0"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#mobileSidebar"
                    aria-controls="mobileSidebar"
                    aria-label="Open sidebar">
                <i class="bi bi-list" style="font-size: 1.5rem;"></i>
            </button>
            
            <!-- Search (Optional, Desktop Only) -->
            @if($showSearch)
                <div class="topbar-search d-none d-md-block">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Search..." aria-label="Search">
                    </div>
                </div>
            @endif
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <!-- Notifications -->
            <div class="dropdown" id="notificationDropdown">
                <button class="btn btn-link position-relative p-2 text-muted border-0"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        title="Notifications">
                    <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                    @php
                        $unreadCount = auth()->user()->unreadNotifications()->count();
                    @endphp
                    <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge {{ $unreadCount > 0 ? '' : 'd-none' }}"
                          style="font-size: 0.5rem; padding: 0.125rem 0.25rem; min-width: 1rem;"
                          data-count="{{ $unreadCount }}">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                    <span class="visually-hidden">New alerts</span>
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

            <!-- Profile Dropdown -->
            <div class="dropdown">
                <button class="btn btn-link d-flex align-items-center gap-2 text-decoration-none border-0 p-2 dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-semibold"
                         style="width: 32px; height: 32px; font-size: 0.875rem;">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="d-none d-lg-inline text-muted small">{{ $user->name ?? 'User' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="min-width: 200px;">
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger w-100 text-start">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
