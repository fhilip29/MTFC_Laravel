<footer class="bg-[#ABA9A9] text-white py-12 border-t border-gray-700">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-10">
        <!-- Logo & Info -->
        <div>
        <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="Logo" class="w-50 h-12 object-cover">
            <p class="text-sm text-gray-400">&copy; 2024 ActiveGym. All rights reserved.</p>
            <div class="mt-2 text-sm space-x-4">
                <a href="/terms" class="hover:underline">Terms</a>
                <a href="/privacypolicy" class="hover:underline">Privacy</a>
            </div>
        </div>

        <!-- Location -->
        <div>
            <h3 class="text-lg font-semibold mb-2">Location</h3>
            <p class="text-sm">Adamson University</p>
            <p class="text-sm">900 San Marcelino St., Ermita, Manila 1000</p>
            <p class="text-sm">+639 27 xxx xxxx</p>
            <p class="text-sm">fhilipkr.lorenzo@adamson.edu.ph</p>
        </div>

        <!-- Links + Social -->
        <div>
            <h3 class="text-lg font-semibold mb-2">Quick Links</h3>
            <ul class="space-y-1 text-sm">
                <li><a href="/" class="hover:underline">Home</a></li>
                <li><a href="/about" class="hover:underline">About</a></li>
                <li><a href="/classes" class="hover:underline">Classes</a></li>
                <li><a href="/trainer" class="hover:underline">Trainer</a></li>
                <li><a href="/pricing" class="hover:underline">Pricing</a></li>
            </ul>
            <div class="flex space-x-3 mt-4">
                <a href="https://www.facebook.com/profile.php?id=100064094075912" target="_blank">
                    <img src="{{ asset('assets/fb.png') }}" alt="FB" class="w-8 h-8 hover:opacity-80">
                </a>
                <a href="https://www.instagram.com/manilatotalfc?igsh=ZWhoeWphanZtdDFw" target="_blank">
                    <img src="{{ asset('assets/ig.png') }}" alt="IG" class="w-8 h-8 hover:opacity-80">
                </a>
            </div>
        </div>
    </div>
</footer>
