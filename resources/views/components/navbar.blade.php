<nav class="navbar-guest position-sticky top-0" x-data="{ mobileMenuOpen: false }">
    <div class="container-fluid px-4 px-md-5">
        <div class="navbar-guest__inner d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('landing') }}" class="navbar-guest__brand text-decoration-none">
                    <span class="navbar-guest__brand-text">Your Mind Aid</span>
                </a>
            </div>
            
            <div class="d-none d-md-flex align-items-center gap-4">
                <a href="{{ route('landing') }}#about" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium">About</a>
                <a href="{{ route('landing') }}#doctors" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium">Doctors</a>
                <a href="{{ route('articles.public.index') }}" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium">Articles</a>
                <a href="{{ route('shop.products') }}" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium">Products</a>
                <a href="{{ route('landing') }}#contact" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium">Contact</a>
                <a href="{{ route('shop.cart') }}" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium d-flex align-items-center gap-1">
                    <i class="bi bi-cart3"></i> Cart
                    @php $cartCount = array_sum(session('cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="badge bg-primary rounded-pill">{{ $cartCount }}</span>
                    @endif
                </a>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link navbar-guest-link text-stone-700 p-0 border-0 text-decoration-none fw-medium">Logout</button>
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
    <div x-show="mobileMenuOpen" x-cloak class="d-md-none border-top border-stone-200 bg-white" style="border-color: var(--color-soft-gray-200, #e2e8f0);">
        <div class="px-4 py-3 d-flex flex-column gap-2">
            <a href="{{ route('landing') }}#about" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium py-2">About</a>
            <a href="{{ route('landing') }}#doctors" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium py-2">Doctors</a>
            <a href="{{ route('articles.public.index') }}" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium py-2">Articles</a>
            <a href="{{ route('shop.products') }}" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium py-2">Products</a>
            <a href="{{ route('landing') }}#contact" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium py-2">Contact</a>
            <a href="{{ route('shop.cart') }}" class="navbar-guest-link text-stone-700 text-decoration-none fw-medium py-2 d-flex align-items-center gap-2">
                <i class="bi bi-cart3"></i> Cart
                @php $cartCount = array_sum(session('cart', [])); @endphp
                @if($cartCount > 0)
                    <span class="badge bg-primary rounded-pill">{{ $cartCount }}</span>
                @endif
            </a>
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
