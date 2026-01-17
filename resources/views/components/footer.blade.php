<footer class="bg-stone-800 text-stone-200 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-xl font-bold text-white mb-4">Your Mind Aid</h3>
                <p class="text-stone-400">
                    Providing compassionate mental health care with a focus on healing and growth.
                </p>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('landing') }}#about" class="text-stone-400 hover:text-teal-400 transition-colors">About Us</a></li>
                    <li><a href="{{ route('landing') }}#doctors" class="text-stone-400 hover:text-teal-400 transition-colors">Our Doctors</a></li>
                    <li><a href="{{ route('landing') }}#contact" class="text-stone-400 hover:text-teal-400 transition-colors">Contact</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Contact</h4>
                <p class="text-stone-400">
                    Email: info@yourmindaid.com<br>
                    Phone: (555) 123-4567
                </p>
            </div>
        </div>
        
        <div class="mt-8 pt-8 border-t border-stone-700 text-center text-stone-400">
            <p>&copy; {{ date('Y') }} Your Mind Aid. All rights reserved.</p>
        </div>
    </div>
</footer>
