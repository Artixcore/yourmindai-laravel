<footer class="bg-dark text-light mt-5">
    <div class="container-fluid px-4 px-md-5 py-5">
        <div class="row g-4">
            <div class="col-md-4">
                <h3 class="h5 fw-bold text-white mb-3">Your Mind Aid</h3>
                <p class="text-muted">
                    Providing compassionate mental health care with a focus on healing and growth.
                </p>
            </div>
            
            <div class="col-md-4">
                <h4 class="h6 fw-semibold text-white mb-3">Quick Links</h4>
                <ul class="list-unstyled d-flex flex-column gap-2">
                    <li><a href="{{ route('landing') }}#about" class="text-muted text-decoration-none hover-text-teal-400">About Us</a></li>
                    <li><a href="{{ route('landing') }}#doctors" class="text-muted text-decoration-none hover-text-teal-400">Our Doctors</a></li>
                    <li><a href="{{ route('landing') }}#contact" class="text-muted text-decoration-none hover-text-teal-400">Contact</a></li>
                </ul>
            </div>
            
            <div class="col-md-4">
                <h4 class="h6 fw-semibold text-white mb-3">Contact</h4>
                <p class="text-muted">
                    Email: info@yourmindaid.com<br>
                    Phone: (555) 123-4567
                </p>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-top border-secondary text-center text-muted">
            <p>&copy; {{ date('Y') }} Your Mind Aid. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
    .hover-text-teal-400:hover {
        color: #2dd4bf !important;
    }
</style>
