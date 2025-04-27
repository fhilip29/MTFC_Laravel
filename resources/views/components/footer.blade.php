<footer class="bg-white text-black py-12 border-t border-gray-200 w-full mt-auto">
    <!-- Scroll to top button -->
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
        class="fixed bottom-8 right-8 bg-red-500 p-3 rounded-full shadow-lg hover:bg-red-600 transition-colors duration-300 z-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-10">
        <!-- Logo & Info -->
        <div>
            <a href="/" class="inline-block">
                <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="Logo" class="w-40 h-auto object-cover">
            </a>
            <p class="text-sm text-black mt-2">&copy; 2023 Manila Total Fitness Center. All rights reserved.</p>
            <div class="flex space-x-3 mt-4">
                <a href="https://www.facebook.com/profile.php?id=100064094075912" target="_blank" class="transform hover:scale-110 transition-transform duration-300">
                    <img src="{{ asset('assets/fb.png') }}" alt="FB" class="w-8 h-8">
                </a>
                <a href="https://www.instagram.com/manilatotalfc?igsh=ZWhoeWphanZtdDFw" target="_blank" class="transform hover:scale-110 transition-transform duration-300">
                    <img src="{{ asset('assets/ig.png') }}" alt="IG" class="w-8 h-8">
                </a>
            </div>
        </div>

        <!-- Location -->
        <div>
            <h3 class="text-lg font-semibold mb-4">Location</h3>
            <p class="text-sm font-semibold">Adamson University</p>
            <p class="text-sm font-semibold">900 San Marcelino St., Ermita, Manila 1000</p>
            <p class="text-sm font-semibold">+639 27 xxx xxxx</p>
            <p class="text-sm font-semibold">fhilipkr.lorenzo@adamson.edu.ph</p>
        </div>

        <!-- Legal -->
        <div>
            <h3 class="text-lg font-semibold mb-4">Legal</h3>
            <div class="space-y-2">
                <div><a href="/terms" class="text-black hover:text-red-500 transition">Terms of Use</a></div>
                <div><a href="/privacypolicy" class="text-black hover:text-red-500 transition">Privacy Policy</a></div>
            </div>
        </div>

        <!-- Quick Links -->
        <div>
            <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
            <ul class="space-y-3 text-sm">
                <li><a href="/" class="text-black hover:text-red-500 transition">Home</a></li>
                <li><a href="/about" class="text-black hover:text-red-500 transition">About</a></li>
                <li><a href="/classes" class="text-black hover:text-red-500 transition">Classes</a></li>
                <li><a href="/trainer" class="text-black hover:text-red-500 transition">Trainer</a></li>
                <li><a href="/pricing" class="text-black hover:text-red-500 transition">Pricing</a></li>
            </ul>
        </div>
    </div>
</footer>
