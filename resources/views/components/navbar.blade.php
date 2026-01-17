<nav class="bg-white shadow-sm border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="{{ route('landing') }}" class="flex items-center space-x-2">
                    <span class="text-2xl font-bold bg-gradient-to-r from-teal-700 to-indigo-700 bg-clip-text text-transparent">
                        Your Mind Aid
                    </span>
                </a>
            </div>
            
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('landing') }}#about" class="text-stone-700 hover:text-teal-700 transition-colors">About</a>
                <a href="{{ route('landing') }}#doctors" class="text-stone-700 hover:text-teal-700 transition-colors">Doctors</a>
                <a href="{{ route('landing') }}#contact" class="text-stone-700 hover:text-teal-700 transition-colors">Contact</a>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="text-stone-700 hover:text-teal-700 transition-colors">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-stone-700 hover:text-teal-700 transition-colors">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-teal-700 text-white rounded-lg hover:bg-teal-800 transition-colors">
                        Login
                    </a>
                @endauth
            </div>
            
            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-stone-700 hover:text-teal-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div x-data="{ mobileMenuOpen: false }" x-show="mobileMenuOpen" x-cloak class="md:hidden border-t border-stone-200">
        <div class="px-4 py-3 space-y-2">
            <a href="{{ route('landing') }}#about" class="block text-stone-700 hover:text-teal-700">About</a>
            <a href="{{ route('landing') }}#doctors" class="block text-stone-700 hover:text-teal-700">Doctors</a>
            <a href="{{ route('landing') }}#contact" class="block text-stone-700 hover:text-teal-700">Contact</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block text-stone-700 hover:text-teal-700">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left text-stone-700 hover:text-teal-700">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-4 py-2 bg-teal-700 text-white rounded-lg text-center">Login</a>
            @endauth
        </div>
    </div>
</nav>

<style>
    [x-cloak] { display: none !important; }
</style>
