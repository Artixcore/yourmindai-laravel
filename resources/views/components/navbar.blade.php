<nav class="bg-white shadow-soft border-bottom border-stone-200 position-sticky top-0" style="z-index: 1000; background: linear-gradient(to bottom, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%) !important; backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
    <div class="container-fluid px-4 px-md-5">
        <div class="d-flex justify-content-between align-items-center" style="height: 64px;">
            <div class="d-flex align-items-center">
                <a href="{{ route('landing') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <span class="fs-2 fw-bold text-psychological-primary" style="background: linear-gradient(135deg, var(--color-teal-700) 0%, var(--color-soft-blue-600) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        Your Mind Aid
                    </span>
                </a>
            </div>
            
            <div class="d-none d-md-flex align-items-center gap-4">
                <a href="{{ route('landing') }}#about" class="text-stone-700 hover-text-teal-700 text-decoration-none fw-medium" style="transition: color 0.3s ease;">About</a>
                <a href="{{ route('landing') }}#doctors" class="text-stone-700 hover-text-teal-700 text-decoration-none fw-medium" style="transition: color 0.3s ease;">Doctors</a>
                <a href="{{ route('landing') }}#contact" class="text-stone-700 hover-text-teal-700 text-decoration-none fw-medium" style="transition: color 0.3s ease;">Contact</a>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="text-stone-700 hover-text-teal-700 text-decoration-none fw-medium" style="transition: color 0.3s ease;">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link text-stone-700 hover-text-teal-700 p-0 border-0 text-decoration-none fw-medium" style="transition: color 0.3s ease;">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-gradient-primary">
                        Login
                    </a>
                @endauth
            </div>
            
            <!-- Mobile menu button -->
            <div class="d-md-none">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="btn btn-link text-stone-700 hover-text-teal-700 border-0 p-2" style="transition: color 0.3s ease;">
                    <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div x-data="{ mobileMenuOpen: false }" x-show="mobileMenuOpen" x-cloak class="d-md-none border-top border-stone-200 bg-white">
        <div class="px-4 py-3 d-flex flex-column gap-2">
            <a href="{{ route('landing') }}#about" class="text-stone-700 hover-text-teal-700 text-decoration-none fw-medium py-2" style="transition: color 0.3s ease;">About</a>
            <a href="{{ route('landing') }}#doctors" class="text-stone-700 hover-text-teal-700 text-decoration-none fw-medium py-2" style="transition: color 0.3s ease;">Doctors</a>
            <a href="{{ route('landing') }}#contact" class="text-stone-700 hover-text-teal-700 text-decoration-none fw-medium py-2" style="transition: color 0.3s ease;">Contact</a>
            @auth
                <a href="{{ route('dashboard') }}" class="text-stone-700 hover-text-teal-700 text-decoration-none fw-medium py-2" style="transition: color 0.3s ease;">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-stone-700 hover-text-teal-700 p-0 border-0 text-start text-decoration-none w-100 fw-medium py-2" style="transition: color 0.3s ease;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-gradient-primary text-center mt-2">Login</a>
            @endauth
        </div>
    </div>
</nav>

<style>
    [x-cloak] { display: none !important; }
</style>
