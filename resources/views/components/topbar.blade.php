@props(['user', 'showSearch' => false])

<div class="topbar position-fixed top-0 start-0 end-0" style="z-index: 1050;">
    <div class="d-flex align-items-center justify-content-between px-4 px-md-5 h-100">
        <div class="d-flex align-items-center gap-3">
            <!-- Mobile Menu Toggle -->
            <button @click="sidebarOpen = !sidebarOpen" class="btn btn-link d-md-none text-muted p-2 border-0" type="button">
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
            <button class="btn btn-link position-relative p-2 text-muted border-0" type="button" title="Notifications">
                <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem; padding: 0.125rem 0.25rem;">
                    <span class="visually-hidden">New alerts</span>
                </span>
            </button>
            
            <!-- Profile Dropdown -->
            <div class="dropdown position-relative" x-data="dropdown(false)">
                <button 
                    @click="toggle()" 
                    class="btn btn-link d-flex align-items-center gap-2 text-decoration-none border-0 p-2" 
                    type="button"
                    aria-expanded="false"
                >
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-semibold" style="width: 32px; height: 32px; font-size: 0.875rem;">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="d-none d-lg-inline text-muted small">{{ $user->name ?? 'User' }}</span>
                    <i class="bi bi-chevron-down text-muted d-none d-lg-inline" style="font-size: 0.75rem;"></i>
                </button>
                
                <div 
                    x-show="open"
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    @click.away="close()"
                    class="dropdown-menu dropdown-menu-end shadow-sm border position-absolute"
                    style="display: none; min-width: 200px; margin-top: 0.5rem; z-index: 1060; top: 100%; right: 0;"
                >
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger w-100 text-start border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
