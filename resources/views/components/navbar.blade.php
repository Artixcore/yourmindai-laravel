{{-- Single responsive header: logo left, nav center/right, cart + login right --}}
@php $cartCount = array_sum(session('cart', [])); @endphp
<header class="navbar-guest position-sticky top-0" role="banner" x-data="{ mobileMenuOpen: false }">
    <nav class="navbar-guest__wrapper" role="navigation" aria-label="Main">
        <div class="navbar-guest__inner">
            <a href="{{ route('landing') }}" class="navbar-guest__brand">
                <!-- <span class="navbar-guest__brand-text">Your Mind Aid</span> -->
                <img src="https://storaeall.s3.us-east-1.amazonaws.com/public/mindaidlogo.png" alt="Your Mind Aid" class="navbar-guest__brand-logo">
                <span class="navbar-guest__brand-text">Your Mind Aid</span>
            </a>

            <ul class="navbar-guest__links d-none d-md-flex list-unstyled mb-0">
                <li><a href="{{ route('landing') }}#about" class="navbar-guest__link">About</a></li>
                <li><a href="{{ route('landing') }}#doctors" class="navbar-guest__link">Doctors</a></li>
                <li><a href="{{ route('articles.public.index') }}" class="navbar-guest__link">Articles</a></li>
                <li><a href="{{ route('shop.products') }}" class="navbar-guest__link">Products</a></li>
                <li><a href="{{ route('landing') }}#contact" class="navbar-guest__link">Contact</a></li>
            </ul>

            <div class="navbar-guest__actions d-none d-md-flex align-items-center">
                <a href="{{ route('shop.cart') }}" class="navbar-guest__cart" aria-label="{{ $cartCount > 0 ? 'Shopping cart, ' . $cartCount . ' ' . ($cartCount === 1 ? 'item' : 'items') : 'Shopping cart' }}">
                    <i class="bi bi-cart3" aria-hidden="true"></i>
                    @if($cartCount > 0)
                        <span class="navbar-guest__cart-count">{{ $cartCount }}</span>
                    @endif
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="navbar-guest__link">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline ms-2">
                        @csrf
                        <button type="submit" class="navbar-guest__btn navbar-guest__btn--ghost">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="navbar-guest__btn navbar-guest__btn--primary">Login</a>
                @endauth
            </div>

            <button type="button" class="navbar-guest__toggle d-md-none" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen" aria-controls="navbar-mobile-menu" aria-label="Toggle menu">
                <span class="navbar-guest__toggle-bar"></span>
                <span class="navbar-guest__toggle-bar"></span>
                <span class="navbar-guest__toggle-bar"></span>
            </button>
        </div>
    </nav>

    <div id="navbar-mobile-menu" class="navbar-guest__mobile d-md-none" x-show="mobileMenuOpen" x-cloak>
        <div class="navbar-guest__mobile-inner">
            <a href="{{ route('landing') }}#about" class="navbar-guest__mobile-link">About</a>
            <a href="{{ route('landing') }}#doctors" class="navbar-guest__mobile-link">Doctors</a>
            <a href="{{ route('articles.public.index') }}" class="navbar-guest__mobile-link">Articles</a>
            <a href="{{ route('shop.products') }}" class="navbar-guest__mobile-link">Products</a>
            <a href="{{ route('landing') }}#contact" class="navbar-guest__mobile-link">Contact</a>
            <a href="{{ route('shop.cart') }}" class="navbar-guest__mobile-link d-flex align-items-center justify-content-between">
                <span>Cart</span>
                @if($cartCount > 0)
                    <span class="badge rounded-pill bg-primary">{{ $cartCount }}</span>
                @endif
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="navbar-guest__mobile-link">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="navbar-guest__mobile-link text-start w-100 border-0 bg-transparent">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="navbar-guest__btn navbar-guest__btn--primary w-100 text-center mt-2">Login</a>
            @endauth
        </div>
    </div>
</header>
