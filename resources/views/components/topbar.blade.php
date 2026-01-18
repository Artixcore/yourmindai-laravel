@props(['user'])

<div class="h-16 bg-white border-bottom border-stone-200 position-fixed top-0 start-0 end-0" style="z-index: 1050;">
    <div class="d-flex align-items-center justify-content-between px-4 px-md-5 h-100">
        <div class="d-flex align-items-center">
            <button @click="sidebarOpen = !sidebarOpen" class="btn btn-link d-md-none text-stone-700 hover-text-teal-700 p-2 border-0">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <!-- Notifications -->
            <button class="btn btn-link position-relative p-2 text-stone-700 hover-text-teal-700 border-0">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                    <span class="visually-hidden">New alerts</span>
                </span>
            </button>
            
            <!-- Profile Dropdown -->
            <div class="dropdown" x-data="dropdown(false)">
                <button @click="toggle()" class="btn btn-link d-flex align-items-center gap-2 text-stone-700 hover-text-teal-700 border-0 text-decoration-none" type="button">
                    <div class="rounded-circle bg-teal-700 d-flex align-items-center justify-content-center text-white fw-semibold" style="width: 32px; height: 32px;">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="d-none d-md-inline">{{ $user->name ?? 'User' }}</span>
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
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
                    class="dropdown-menu dropdown-menu-end shadow border border-stone-200"
                    style="display: none; min-width: 192px;"
                >
                    <a href="#" class="dropdown-item text-stone-700 hover-bg-teal-50 hover-text-teal-700">Profile</a>
                    <a href="#" class="dropdown-item text-stone-700 hover-bg-teal-50 hover-text-teal-700">Settings</a>
                    <hr class="dropdown-divider">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-stone-700 hover-bg-teal-50 hover-text-teal-700 w-100 text-start border-0 bg-transparent">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
