<footer class="footer-guest">
    <div class="container-fluid px-4 px-md-5 py-5">
        <div class="row g-4 g-lg-5">
            <div class="col-12 col-md-4">
                <h3 class="footer-guest__title">Your Mind Aid</h3>
                <p class="footer-guest__text">
                    Providing compassionate mental health care with a focus on healing and growth.
                </p>
            </div>
            <div class="col-12 col-md-4">
                <h4 class="footer-guest__heading">Quick Links</h4>
                <ul class="footer-guest__links list-unstyled">
                    <li><a href="{{ route('landing') }}#about" class="footer-guest__link">About Us</a></li>
                    <li><a href="{{ route('landing') }}#doctors" class="footer-guest__link">Our Doctors</a></li>
                    <li><a href="{{ route('articles.public.index') }}" class="footer-guest__link">Articles</a></li>
                    <li><a href="{{ route('shop.products') }}" class="footer-guest__link">Products</a></li>
                    <li><a href="{{ route('landing') }}#contact" class="footer-guest__link">Contact</a></li>
                </ul>
            </div>
            <div class="col-12 col-md-4">
                <h4 class="footer-guest__heading">Contact</h4>
                <p class="footer-guest__text">
                    Email: info@yourmindaid.com<br>
                    Phone: (555) 123-4567
                </p>
            </div>
        </div>
        <div class="footer-guest__bottom mt-5 pt-4">
            <p class="footer-guest__copyright mb-0">
                &copy; {{ date('Y') }} Your Mind Aid. All rights reserved.
                <span class="d-none d-md-inline"> &middot; </span>
                <span class="d-block d-md-inline mt-1 mt-md-0">Developed by <a href="https://artixcore.com" target="_blank" rel="noopener noreferrer" class="footer-guest__link">Artixcore</a></span>
            </p>
        </div>
    </div>
</footer>
