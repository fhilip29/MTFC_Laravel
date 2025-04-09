@php
    $user = Auth::user();
    $hideFooter = request()->is('profile*') || ($user && $user->user_type === 'admin');
@endphp

@if (!$hideFooter)
<footer>
    <div class="w-full h-[313px] border-t text-black flex flex-row px-10 items-center mt-auto">
        <div class="flex flex-col items-start w-full">
            <img
                src="{{ asset('assets/logo.png') }}"
                alt="Logo"
                class="object-cover"
                style="height: 60px; width: 100px;"
            />

            <div class="text-[15px] mt-16" style="font-family: Inter, sans-serif;">
                <p>&copy; 2024.</p>
                <p>All rights reserved. ActiveGym.</p>
                <div class="flex flex-row mt-5 space-x-8">
                    <a href="{{ url('/terms') }}" class="no-underline text-black font-normal">Terms of Use</a>
                    <a href="{{ url('/privacypolicy') }}" class="no-underline text-black font-normal">Privacy Policy</a>
                </div>
            </div>
        </div>

        <div class="flex flex-row space-x-10 justify-evenly w-full">
            <div class="flex flex-col justify-start">
                <h2 class="font-bold text-[15px]">LOCATION</h2>
                <div class="mt-3">
                    <p class="font-normal">
                        Adamson University <br />
                        900 San Marcelino St., Ermita, <br />
                        Manila 1000
                    </p>
                </div>
                <div class="mt-3">
                    <p>+639 27 xxx xxxx</p>
                </div>
                <div class="mt-3">
                    <p>fhilipkr.lorenzo@adamson.edu.ph</p>
                </div>
            </div>

            <div class="flex flex-col justify-start">
                <h2 class="font-bold text-[15px]">QUICK LINKS</h2>
                <div class="flex flex-col items-center justify-center space-y-3 mt-3">
                    <a href="{{ url('/') }}" class="no-underline text-black font-normal">Home</a>
                    <a href="{{ url('/about') }}" class="no-underline text-black font-normal">About</a>
                    <a href="{{ url('/classes') }}" class="no-underline text-black font-normal">Classes</a>
                    <a href="{{ url('/trainer') }}" class="no-underline text-black font-normal">Trainer</a>
                    <a href="{{ url('/pricing') }}" class="no-underline text-black font-normal">Pricing</a>
                </div>
            </div>
        </div>

        <div class="flex flex-row w-full justify-center mb-16 self-end space-x-2">
            <a href="https://www.facebook.com/profile.php?id=100064094075912" target="_blank" rel="noopener noreferrer">
                <img
                    src="{{ asset('assets/fb.png') }}"
                    alt="Facebook Logo"
                    class="object-cover"
                    style="height: 33px; width: 34px;"
                />
            </a>
            <a href="https://www.instagram.com/manilatotalfc?igsh=ZWhoeWphanZtdDFw" target="_blank" rel="noopener noreferrer">
                <img
                    src="{{ asset('assets/ig.png') }}"
                    alt="Instagram Logo"
                    class="object-cover"
                    style="height: 33px; width: 34px;"
                />
            </a>
        </div>
    </div>
</footer>
@endif
