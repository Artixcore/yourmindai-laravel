@props(['user'])

<div class="h-16 bg-white border-b border-stone-200 fixed top-0 left-0 right-0 z-40 flex items-center justify-between px-6">
    <div class="flex items-center">
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-stone-700 hover:text-teal-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
    
    <div class="flex items-center space-x-4">
        <!-- Notifications -->
        <button class="relative p-2 text-stone-700 hover:text-teal-700 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>
        
        <!-- Profile Dropdown -->
        <div x-data="dropdown(false)" class="relative">
            <button @click="toggle()" class="flex items-center space-x-2 text-stone-700 hover:text-teal-700 transition-colors">
                <div class="w-8 h-8 bg-teal-700 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                <span class="hidden md:block">{{ $user->name ?? 'User' }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-stone-200 py-1 z-50"
                style="display: none;"
            >
                <a href="#" class="block px-4 py-2 text-sm text-stone-700 hover:bg-teal-50 hover:text-teal-700">Profile</a>
                <a href="#" class="block px-4 py-2 text-sm text-stone-700 hover:bg-teal-50 hover:text-teal-700">Settings</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-stone-700 hover:bg-teal-50 hover:text-teal-700">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
