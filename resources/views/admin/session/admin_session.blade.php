@extends('layouts.admin')

@section('title', 'Session Management')

@section('content')
<div class="container mx-auto px-4 py-6 sm:py-8">
    <div class="bg-[#1F2937] shadow-lg rounded-xl p-4 sm:p-6 border border-[#374151]">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-2 mb-4">
            <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-calendar-alt text-[#9CA3AF]"></i> Session Management
            </h1>
            <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-2">
                <button id="scanButton" class="w-full sm:w-auto bg-[#374151] hover:bg-[#4B5563] text-white px-4 py-2 rounded-md shadow-md flex items-center justify-center gap-2 transition-colors">
                    <i class="fas fa-qrcode"></i> Scan
                </button>
                <button id="guestButton" class="w-full sm:w-auto bg-[#374151] hover:bg-[#4B5563] text-white px-4 py-2 rounded-md shadow-md flex items-center justify-center gap-2 transition-colors">
                    <i class="fas fa-user-plus"></i> Guest
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Search by name..." 
                    class="w-full p-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] shadow-sm"
                >
            </div>
            <div>
                <select 
                    id="roleFilter" 
                    class="w-full p-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] shadow-sm"
                >
                    <option value="">All Roles</option>
                    <option value="member">Member</option>
                    <option value="trainer">Trainer</option>
                    <option value="admin">Admin</option>
                    <option value="guest">Guest</option>
                </select>
            </div>
            <div>
                <input 
                    type="date" 
                    id="dateFilter" 
                    placeholder="Filter by date"
                    class="w-full p-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] shadow-sm"
                >
            </div>
            <div>
                <button id="resetFilters" class="w-full p-3 bg-[#374151] hover:bg-[#4B5563] text-[#9CA3AF] hover:text-white rounded-md shadow-md flex items-center justify-center gap-2 transition-colors">
                    <i class="fas fa-undo-alt"></i> Reset Filters
                </button>
            </div>
        </div>

        <div class="-mx-4 sm:mx-0 overflow-x-auto bg-[#1F2937] rounded-lg shadow-md">
            <div class="min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-[#374151] text-sm text-left" id="sessionTable">
                        <thead class="bg-[#374151] text-[#9CA3AF] uppercase sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3">Profile</th>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Time</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#374151]">
                            @foreach ($sessions as $session)
                                @php
                                    // Determine avatar background color based on role
                                    $roleBgColor = 'bg-gray-600'; // Default for Guest or unknown
                                    if ($session->user) {
                                        switch ($session->user->role) {
                                            case 'member':
                                                $roleBgColor = 'bg-blue-600';
                                                break;
                                            case 'trainer':
                                                $roleBgColor = 'bg-purple-600';
                                                break;
                                            case 'admin': // Assuming an admin role might exist
                                                $roleBgColor = 'bg-yellow-600';
                                                break;
                                        }
                                    }
                                @endphp
                                <tr class="hover:bg-[#374151] transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            @if($session->user && $session->user->profile_image)
                                                <img src="{{ asset($session->user->profile_image) }}" alt="{{ $session->user->full_name }}" class="h-9 w-9 rounded-full object-cover">
                                            @else
                                                <div class="h-9 w-9 rounded-full {{ $roleBgColor }} flex items-center justify-center text-white font-bold text-xs">
                                                    {{ $session->user ? strtoupper(substr($session->user->full_name, 0, 2)) : 'G' }} 
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-white">{{ $session->user->full_name ?? ($session->guest_name ?? 'Guest User') }}</td>
                                    <td class="px-4 py-3 text-[#9CA3AF] capitalize">{{ $session->user->role ?? 'guest' }}</td> 
                                    <td class="px-4 py-3 text-[#9CA3AF]">{{ \Carbon\Carbon::parse($session->time)->setTimezone('Asia/Manila')->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-[#9CA3AF]">{{ \Carbon\Carbon::parse($session->time)->setTimezone('Asia/Manila')->format('h:i A') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $session->status === 'IN' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                            @if($session->status === 'IN')
                                                <i class="fas fa-arrow-right text-xs"></i>
                                            @else
                                                <i class="fas fa-arrow-left text-xs"></i>
                                            @endif
                                            {{ $session->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Scanner Modal -->
<div id="scannerModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div id="scannerModalOverlay" class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-black opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-[#1F2937] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[#374151]">
            <div class="bg-[#1F2937] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-white flex items-center justify-between relative z-10">
                            <span>QR Code Scanner</span>
                            <button id="closeScanner" class="text-[#9CA3AF] hover:text-white transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </h3>
                        
                        <!-- Time In/Out Selection - Moved to top with improved design -->
                        <div class="mt-4 mb-6 relative z-10">
                            <p class="text-white mb-3 text-center">Select action before scanning:</p>
                            <div class="flex gap-4 justify-center">
                                <button id="timeInBtn" class="py-3 px-5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md shadow active:bg-green-800 flex items-center gap-2 transition-colors flex-1 justify-center">
                                    <i class="fas fa-sign-in-alt text-lg"></i> 
                                    <span class="text-md font-bold">Check In</span>
                                </button>
                                <button id="timeOutBtn" class="py-3 px-5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md shadow active:bg-red-800 flex items-center gap-2 transition-colors flex-1 justify-center">
                                    <i class="fas fa-sign-out-alt text-lg"></i> 
                                    <span class="text-md font-bold">Check Out</span>
                                </button>
                            </div>
                            <div class="text-center mt-2 text-white text-sm">
                                <span class="font-medium" id="selected-mode-label">Current mode: Check In</span>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div id="scanner-container" class="relative overflow-hidden rounded-lg bg-black aspect-square w-full max-w-md mx-auto">
                                <video id="scanner-video" class="w-full h-full object-cover"></video>
                                <canvas id="scanner-canvas" class="hidden absolute top-0 left-0"></canvas>
                                <div id="scanner-overlay" class="absolute inset-0 border-2 border-transparent flex items-center justify-center">
                                    <div class="w-2/3 h-2/3 border-2 border-green-500 relative">
                                        <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-green-500"></div>
                                        <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-green-500"></div>
                                        <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-green-500"></div>
                                        <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-green-500"></div>
                                    </div>
                                </div>
                                <div id="scan-animation" class="absolute top-0 left-0 w-full h-1 bg-green-500 opacity-75 transform -translate-y-full"></div>
                            </div>
                            <div id="scanner-message" class="mt-4 text-center text-white relative z-10">Position the QR code within the frame</div>
                            
                            <!-- Removed the buttons from here since they're now above the scanner -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-[#111827] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse relative z-10">
                <button id="startScanner" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Start Scanner
                </button>
                <button id="cancelScanner" class="mt-3 w-full inline-flex justify-center rounded-md border border-[#374151] shadow-sm px-4 py-2 bg-[#1F2937] text-base font-medium text-[#9CA3AF] hover:bg-[#374151] focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Guest Check-in/out Modal -->
<div id="guestModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div id="guestModalOverlay" class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-black opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-[#1F2937] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[#374151]">
            <div class="bg-[#1F2937] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-white flex items-center justify-between relative z-30">
                            <span>Guest Management</span>
                            <button id="closeGuestModal" class="text-[#9CA3AF] hover:text-white transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </h3>
                        
                        <!-- Tab Navigation -->
                        <div class="flex border-b border-[#374151] mt-4 relative z-30">
                            <button id="checkInTabBtn" class="py-2 px-4 border-b-2 border-blue-500 text-white font-medium relative z-30">
                                <i class="fas fa-sign-in-alt mr-2"></i>Check In
                            </button>
                            <button id="checkOutTabBtn" class="py-2 px-4 border-b-2 border-transparent text-[#9CA3AF] hover:text-white relative z-30">
                                <i class="fas fa-sign-out-alt mr-2"></i>Check Out
                            </button>
                        </div>
                        
                        <!-- Check In Tab Content -->
                        <div id="checkInTab" class="mt-4 relative z-20">
                            <label for="guestNameInput" class="block text-sm font-medium text-[#9CA3AF] mb-1">Guest Name</label>
                            <input type="text" id="guestNameInput" placeholder="Enter guest's full name" class="w-full p-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] shadow-sm mb-4">
                            <p id="guestNameError" class="text-red-500 text-xs mt-1 hidden">Guest name is required.</p>
                        
                            <label for="guestPhoneInput" class="block text-sm font-medium text-[#9CA3AF] mb-1">Phone Number</label>
                            <input type="tel" id="guestPhoneInput" placeholder="Philippine Phone Number (e.g., +63 917 123 4567)" class="w-full p-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] shadow-sm mb-4">
                            <p id="guestPhoneError" class="text-red-500 text-xs mt-1 hidden">Valid Philippine phone number is required.</p>
                            
                            <div class="mt-4 flex justify-center">
                                <button id="guestCheckInBtn" class="py-3 px-5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md shadow flex items-center gap-2 transition-colors w-full justify-center relative z-30">
                                    <i class="fas fa-sign-in-alt text-lg"></i>
                                    <span class="text-md font-bold">Check In Guest</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Check Out Tab Content -->
                        <div id="checkOutTab" class="mt-4 relative z-20 hidden">
                            <div class="mb-4">
                                <h4 class="text-white font-medium text-sm mb-2">Currently Checked In Guests</h4>
                                <div id="checkedInGuestsList" class="max-h-72 overflow-y-auto bg-[#111827] rounded-md shadow-inner border border-[#374151] relative z-20">
                                    <!-- Guest list will be populated dynamically -->
                                    <div class="p-4 text-center text-[#9CA3AF] text-sm" id="noGuestsMessage">
                                        <i class="fas fa-info-circle mr-2"></i> Loading guests...
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-[#9CA3AF] italic">
                                <i class="fas fa-info-circle mr-1"></i> Click the "Check Out" button next to a guest to check them out.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-[#111827] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse relative z-30">
                <button id="cancelGuestModal" class="w-full inline-flex justify-center rounded-md border border-[#374151] shadow-sm px-4 py-2 bg-[#1F2937] text-base font-medium text-[#9CA3AF] hover:bg-[#374151] focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Import jsQR library -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<!-- Import SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        const dateFilter = document.getElementById('dateFilter');
        const resetFilters = document.getElementById('resetFilters');
        const rows = document.querySelectorAll('#sessionTable tbody tr');
        
        // Function to apply all filters
        function applyFilters() {
            const searchText = searchInput.value.toLowerCase();
            const roleValue = roleFilter.value.toLowerCase();
            const dateValue = dateFilter.value ? new Date(dateFilter.value) : null;
            
            rows.forEach(row => {
                // Get column values
                const nameCell = row.cells[1];
                const roleCell = row.cells[2];
                const dateCell = row.cells[3];
                
                // Get text for search filter
                const nameText = nameCell ? nameCell.innerText.toLowerCase() : '';
                
                // Get role for role filter
                const roleText = roleCell ? roleCell.innerText.toLowerCase() : '';
                
                // Get date for date filter
                let rowDate = null;
                if (dateCell) {
                    const dateParts = dateCell.innerText.split(',')[0].split(' ');
                    const month = getMonthNumber(dateParts[0]);
                    const day = parseInt(dateParts[1]);
                    const year = parseInt(dateCell.innerText.split(', ')[1]);
                    if (!isNaN(month) && !isNaN(day) && !isNaN(year)) {
                        rowDate = new Date(year, month, day);
                    }
                }
                
                // Check if row passes all filters
                const passesSearch = nameText.includes(searchText);
                const passesRole = !roleValue || roleText === roleValue;
                const passesDate = !dateValue || (rowDate && 
                    rowDate.getFullYear() === dateValue.getFullYear() && 
                    rowDate.getMonth() === dateValue.getMonth() && 
                    rowDate.getDate() === dateValue.getDate());
                
                // Show or hide row based on filter results
                row.style.display = (passesSearch && passesRole && passesDate) ? '' : 'none';
            });
        }
        
        // Helper function to get month number from abbreviation
        function getMonthNumber(monthAbbr) {
            const months = {
                'jan': 0, 'feb': 1, 'mar': 2, 'apr': 3, 'may': 4, 'jun': 5,
                'jul': 6, 'aug': 7, 'sep': 8, 'oct': 9, 'nov': 10, 'dec': 11
            };
            return months[monthAbbr.toLowerCase()];
        }
        
        // Reset all filters and show all rows
        function resetAllFilters() {
            searchInput.value = '';
            roleFilter.value = '';
            dateFilter.value = '';
            
            // Show all rows
            rows.forEach(row => {
                row.style.display = '';
            });
            
            // Show reset toast notification
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            
            Toast.fire({
                icon: 'success',
                title: 'Filters have been reset'
            });
        }
        
        // Add event listeners for all filters
        searchInput.addEventListener('input', applyFilters);
        roleFilter.addEventListener('change', applyFilters);
        dateFilter.addEventListener('change', applyFilters);
        resetFilters.addEventListener('click', resetAllFilters);
        
        // Apply filters on page load (if any values are already set)
        applyFilters();
    });

    // QR Code Scanner Implementation
    document.addEventListener('DOMContentLoaded', function() {
        const scanButton = document.getElementById('scanButton');
        const scannerModal = document.getElementById('scannerModal');
        const closeScanner = document.getElementById('closeScanner');
        const cancelScanner = document.getElementById('cancelScanner');
        const startScannerBtn = document.getElementById('startScanner');
        const timeInBtn = document.getElementById('timeInBtn');
        const timeOutBtn = document.getElementById('timeOutBtn');
        const video = document.getElementById('scanner-video');
        const canvas = document.getElementById('scanner-canvas');
        const scanAnimation = document.getElementById('scan-animation');
        const scannerMessage = document.getElementById('scanner-message');
        const scannerModalOverlay = document.getElementById('scannerModalOverlay');
        
        // Guest Modal Elements
        const guestButton = document.getElementById('guestButton');
        const guestModal = document.getElementById('guestModal');
        const guestModalOverlay = document.getElementById('guestModalOverlay');
        const closeGuestModal = document.getElementById('closeGuestModal');
        const cancelGuestModal = document.getElementById('cancelGuestModal');
        const guestNameInput = document.getElementById('guestNameInput');
        const guestNameError = document.getElementById('guestNameError');
        const guestPhoneInput = document.getElementById('guestPhoneInput');
        const guestPhoneError = document.getElementById('guestPhoneError');
        const guestCheckInBtn = document.getElementById('guestCheckInBtn');
        const guestCheckOutBtn = document.getElementById('guestCheckOutBtn');
        
        // Tab elements
        const checkInTabBtn = document.getElementById('checkInTabBtn');
        const checkOutTabBtn = document.getElementById('checkOutTabBtn');
        const checkInTab = document.getElementById('checkInTab');
        const checkOutTab = document.getElementById('checkOutTab');
        const checkedInGuestsList = document.getElementById('checkedInGuestsList');
        const noGuestsMessage = document.getElementById('noGuestsMessage');
        
        let scanning = false;
        let stream = null;
        let scanMode = 'IN'; // Default scan mode
        let canvasContext = canvas.getContext('2d');
        
        // Initialize SweetAlert Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        
        // Set active scan mode
        function setActiveScanMode(mode) {
            scanMode = mode;
            
            // Remove active class from both buttons
            timeInBtn.classList.remove('ring-2', 'ring-white');
            timeOutBtn.classList.remove('ring-2', 'ring-white');
            
            // Add active class to selected button
            if (mode === 'IN') {
                timeInBtn.classList.add('ring-2', 'ring-white');
                document.getElementById('selected-mode-label').textContent = 'Current mode: Check In';
            } else {
                timeOutBtn.classList.add('ring-2', 'ring-white');
                document.getElementById('selected-mode-label').textContent = 'Current mode: Check Out';
            }
            
            scannerMessage.textContent = `Ready to scan for Time ${mode}`;
            
            // Show notification
            Toast.fire({
                icon: 'info',
                title: `Switched to ${mode === 'IN' ? 'Check-in' : 'Check-out'} mode`
            });
        }
        
        // Set initial active mode
        setActiveScanMode('IN');
        
        // Time In button click
        timeInBtn.addEventListener('click', function() {
            setActiveScanMode('IN');
        });
        
        // Time Out button click
        timeOutBtn.addEventListener('click', function() {
            setActiveScanMode('OUT');
        });
        
        // Show scanner modal
        scanButton.addEventListener('click', function() {
            scannerModal.classList.remove('hidden');
            // Automatically start scanner when modal opens
            if (!scanning) {
                startScanner();
                startScannerBtn.textContent = 'Stop Scanner';
                startScannerBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                startScannerBtn.classList.add('bg-red-600', 'hover:bg-red-700');
            }
        });
        
        // Close scanner modal
        function closeModal() {
            scannerModal.classList.add('hidden');
            stopScanner();
        }
        
        closeScanner.addEventListener('click', closeModal);
        cancelScanner.addEventListener('click', closeModal);
        
        // Add click listener to overlay to close modal
        scannerModalOverlay.addEventListener('click', function(event) {
            // Check if the click is directly on the overlay
            if (event.target === scannerModalOverlay) {
                closeModal();
            }
        });
        
        // Start scanner
        startScannerBtn.addEventListener('click', function() {
            if (scanning) {
                stopScanner();
                startScannerBtn.textContent = 'Start Scanner';
                startScannerBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                startScannerBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            } else {
                startScanner();
                startScannerBtn.textContent = 'Stop Scanner';
                startScannerBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                startScannerBtn.classList.add('bg-red-600', 'hover:bg-red-700');
            }
        });
        
        // Start scanner function
        function startScanner() {
            scannerMessage.textContent = 'Attempting to access camera...';
            scannerMessage.classList.remove('text-red-500');
            
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                // Request camera access directly
                navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: "environment", // Prefer back camera
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    },
                    audio: false
                })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    video.srcObject = mediaStream;
                    video.setAttribute('playsinline', true);
                    
                    // Ensure video starts playing
                    video.onloadedmetadata = function(e) {
                        console.log('Video metadata loaded, attempting to play');
                        video.play()
                            .then(() => {
                                console.log('Video playback started');
                                console.log('Video dimensions:', video.videoWidth, 'x', video.videoHeight);
                                scanning = true;
                                startScanAnimation();
                                scannerMessage.textContent = `Scanning for Time ${scanMode}...`;
                                
                                // Set canvas size to match video
                                canvas.width = video.videoWidth;
                                canvas.height = video.videoHeight;
                                
                                // Start QR code detection loop
                                requestAnimationFrame(scanQRCode);
                            })
                            .catch(err => {
                                console.error('Error playing video:', err);
                                scannerMessage.textContent = 'Error starting video: ' + err.message;
                                scannerMessage.classList.add('text-red-500');
                                
                                // Show explanation to user
                                Swal.fire({
                                    title: 'Camera Error',
                                    text: 'Could not start video stream: ' + err.message,
                                    icon: 'error'
                                });
                            });
                    };
                    
                    // Add additional event listeners for debugging
                    video.addEventListener('play', () => console.log('Video play event fired'));
                    video.addEventListener('error', (e) => {
                        console.error('Video error:', e);
                        scannerMessage.textContent = 'Video error: ' + (video.error ? video.error.message : 'Unknown error');
                        scannerMessage.classList.add('text-red-500');
                    });
                })
                .catch(function(error) {
                    console.error('Error accessing camera:', error);
                    let errorMessage = 'Error accessing camera. ';
                    
                    if (error.name === 'NotAllowedError') {
                        errorMessage += 'Camera permission denied. Please allow camera access in your browser settings and try again.';
                    } else if (error.name === 'NotFoundError') {
                        errorMessage += 'No camera found on this device.';
                    } else if (error.name === 'NotReadableError') {
                        errorMessage += 'Camera is already in use by another application.';
                    } else {
                        errorMessage += error.message || 'Unknown error.';
                    }
                    
                    scannerMessage.textContent = errorMessage;
                    scannerMessage.classList.add('text-red-500');
                    
                    // Show explanation to user
                    Swal.fire({
                        title: 'Camera Access Error',
                        text: errorMessage,
                        icon: 'error'
                    });
                });
            } else {
                scannerMessage.textContent = 'Camera access not supported in this browser.';
                scannerMessage.classList.add('text-red-500');
                
                // Show explanation to user
                Swal.fire({
                    title: 'Browser Not Supported',
                    text: 'Your browser does not support camera access. Please try using Chrome, Firefox, or Edge.',
                    icon: 'error'
                });
            }
        }
        
        // Scan for QR code
        function scanQRCode() {
            if (!scanning) return;
            
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                // Draw current video frame to canvas
                canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                // Get image data for QR code detection
                const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
                
                // Try to detect QR code
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "dontInvert",
                });
                
                if (code) {
                    // QR code detected
                    console.log("QR Code detected:", code.data);
                    
                    // Vibrate if supported
                    if (navigator.vibrate) {
                        navigator.vibrate(100);
                    }
                    
                    // Play a beep sound
                    const beep = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2Hh4NwVlZldoSQkIF0W1Vjc4CNjIV3XVlecXqFioZ8aGRmeX+IiYN3cXR5foODgnyAdHZ9gIGDgXt4dXd8f4GDgn98eHZ4e36BhYaBfXh1dXh8gIaIh4F7dnR2e3+FiYmGgHl0cnZ8gIaKioR+eHR1eX6DiYuKhYB7eXd6f4WJi4iBfXp5e36DiYuLh4J+e3p7f4OGioqGg3+8vr/AwsPExcbHyMnKy8zNzs/Q0dLT1NXW19jZ2tvc3d7f4CBg4SGh4iJiouMjY6PkJGSk5SVlpeYmZqbnJ2en6ChoqOkpaanqKmqq6ytrq+wsbKztLW2t7i5uru8vb6/wMHCw8TFxsfIycrLzM3Oz9DR0tPU1dbX2NnbW9zdXl9f4CBg4SGh4iJiouMjY6PkZKTlP6goaKjpKWmp6j7s7S1tre4ubq7/MbHyMnKy8zNzv3Z2tvc3d7f4GBJRkM/PDgLDhEUFxocHyLwJScoKSssLS7/ODk6Ozw9Pj9A+0pLTE1OT1BR/ltcXV5fYGFiY/ttbm9wcXJzdHX+f4CBgoOEhYaH+5GSk5SVlpeYmf6jo6SlpqeoqKn7s7S1tre4ubq7/MXGx8jJysvMzf3X2Nna29zd3t/v+fr7/P3+/wAB/wsNDg8QERIT+x0eHyAhIiMkJf8vMDEyMzQ1Njf+QUJDREVGSElK/lRVVldYWVpbXP5mZ2hpamtsbW7+eHl6e3x9fn+A/oqLjI2Oj5CRkv2cnZ6foKGio6T9rq+wsbKztLW2/cDBwsPExcbHyP7S09TV1tfY2dr95+jp6uvs7e7v/vn6+/z9/v8AAf0LDA0ODxAREv0cHR4fICEiIyT+Li8wMTIzNDU2/kBBQkNERUZHSP5SUlNUVVZXWFn+Y2RlZmdoaWpr/nV2d3h5ent8ff+HvLzb3fLy/wD8ciEAAAAAABgBAACfAAAAHQAAAB0AAAAdAAAAHQAAAB0AAAAdAAAAHQAAAB0AAAAdAAAAHQAAAB0AAAAdAAAAHQAAAB0AAAAdAAAAHQAAAB0AAAD9////QgAAABsAAQALAAIACgADAAkABAAIAAUABwAGAAYABwAFAAgABAAJAAMAqAGzAb4BqAGMAT8BuAGPAXIBbgE2AbgBjwFVAVkBKwG4AY8BQQFNASEBuAGPAUEBSQEdAbgBjwFBAUYBGgG4AY8BQQFEARcBuAGPAUEBQQEVAbgBjwFBATYBBQG4AY8BQQEWAfwAuAGPAUEBBQHxALgBjwFBAaUBKAGGAY0BQQHZASEBCgO4AY8BQQFuACUBCwO4AY8BQQC+AQUBCwO4AY8BQQDnAfMAeAG3A');
                    beep.play().catch(e => console.log('Audio play error:', e));
                    
                    // Flash overlay
                    const overlay = document.querySelector('#scanner-overlay');
                    overlay.style.borderColor = 'rgba(34, 197, 94, 0.8)'; // Green highlight
                    setTimeout(() => {
                        overlay.style.borderColor = 'transparent';
                    }, 300);
                    
                    processScannedQRCode(code.data);
                    return;
                }
            }
            
            // Continue scanning loop if no QR code found
            requestAnimationFrame(scanQRCode);
        }
        
        // Stop scanner function
        function stopScanner() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
                stream = null;
            }
            scanning = false;
            stopScanAnimation();
            scannerMessage.textContent = `Ready to scan for Time ${scanMode}`;
            scannerMessage.classList.remove('text-red-500', 'text-green-500');
        }
        
        // Process scanned QR code
        function processScannedQRCode(qrCode) {
            stopScanAnimation();
            scannerMessage.textContent = `Processing QR code...`;
            
            // Show processing alert
            Swal.fire({
                title: 'Processing...',
                text: `Processing QR code for Time ${scanMode}`,
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send the QR code to the server
            fetch('{{ route("admin.session.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    qr_code: qrCode,
                    status: scanMode,
                    timezone: 'Asia/Manila' // Explicitly set Philippines timezone
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    handleSuccessfulScan(data.data);
                } else {
                    scannerMessage.textContent = data.error || 'Error processing QR code';
                    scannerMessage.classList.add('text-red-500');
                    
                    Swal.fire({
                        title: 'Error',
                        text: data.error || 'Error processing QR code',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                scannerMessage.textContent = 'Error processing scan. Please try again.';
                scannerMessage.classList.add('text-red-500');
                
                Swal.fire({
                    title: 'Error',
                    text: 'Network error. Please check your connection and try again.',
                    icon: 'error'
                });
            });
            
            scanning = false;
            startScannerBtn.textContent = 'Start Scanner';
            startScannerBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
            startScannerBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        }
        
        // Handle successful scan
        function handleSuccessfulScan(data) {
            scannerMessage.textContent = `Member: ${data.full_name} - Successfully scanned for Time ${data.status}!`;
            scannerMessage.classList.add('text-green-500');
            
            // Log response data for debugging
            console.log('Successful scan data:', data);
            console.log('Profile image URL:', data.profile_image);
            
            // Show success notification
            Swal.fire({
                title: 'Success!',
                text: `${data.full_name} has been checked ${data.status === 'IN' ? 'in' : 'out'} successfully!`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            
            // Add the new session to the table
            const tbody = document.querySelector('#sessionTable tbody');
            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-[#374151] transition-colors';
            
            const date = new Date();
            const options = { timeZone: 'Asia/Manila', month: 'short', day: 'numeric', year: 'numeric' };
            const timeOptions = { timeZone: 'Asia/Manila', hour: 'numeric', minute: 'numeric', hour12: true };
            const formattedDate = date.toLocaleDateString('en-US', options);
            const formattedTime = date.toLocaleTimeString('en-US', timeOptions);
            
            // Determine the role-specific background color
            let roleBgColor = 'bg-gray-600'; // Default
            if (data.role === 'member') {
                roleBgColor = 'bg-blue-600';
            } else if (data.role === 'trainer') {
                roleBgColor = 'bg-purple-600';
            } else if (data.role === 'admin') {
                roleBgColor = 'bg-yellow-600';
            }
            
            // Create the table row with profile image or initials
            let profileCell = '';
            if (data.profile_image) {
                // Use the profile image if available
                profileCell = `
                    <td class="px-4 py-3">
                        <div class="flex items-center">
                            <img src="${data.profile_image}" alt="${data.full_name}" class="h-9 w-9 rounded-full object-cover">
                        </div>
                    </td>
                `;
            } else {
                // Use initials if no profile image
                profileCell = `
                    <td class="px-4 py-3">
                        <div class="flex items-center">
                            <div class="h-9 w-9 rounded-full ${roleBgColor} flex items-center justify-center text-white font-bold text-xs">
                                ${data.full_name.substring(0, 2).toUpperCase()}
                            </div>
                        </div>
                    </td>
                `;
            }
            
            // Build the rest of the row HTML
            newRow.innerHTML = `
                ${profileCell}
                <td class="px-4 py-3 font-medium text-white">${data.full_name}</td>
                <td class="px-4 py-3 text-[#9CA3AF] capitalize">${data.role}</td>
                <td class="px-4 py-3 text-[#9CA3AF]">${formattedDate}</td>
                <td class="px-4 py-3 text-[#9CA3AF]">${formattedTime}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full 
                        ${data.status === 'IN' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}">
                        ${data.status === 'IN' 
                            ? '<i class="fas fa-arrow-right text-xs"></i>' 
                            : '<i class="fas fa-arrow-left text-xs"></i>'}
                        ${data.status}
                    </span>
                </td>
            `;
            
            if (tbody.firstChild) {
                tbody.insertBefore(newRow, tbody.firstChild);
            } else {
                tbody.appendChild(newRow);
            }
        }
        
        // Scan animation
        function startScanAnimation() {
            scanAnimation.style.animation = 'scanline 2s linear infinite';
        }
        
        function stopScanAnimation() {
            scanAnimation.style.animation = 'none';
        }

        // --- Guest Modal Logic ---

        // Function to switch tabs
        function switchToTab(tabName) {
            if (tabName === 'checkIn') {
                checkInTabBtn.classList.add('border-blue-500', 'text-white');
                checkInTabBtn.classList.remove('border-transparent', 'text-[#9CA3AF]');
                checkOutTabBtn.classList.add('border-transparent', 'text-[#9CA3AF]');
                checkOutTabBtn.classList.remove('border-blue-500', 'text-white');
                
                checkInTab.classList.remove('hidden');
                checkOutTab.classList.add('hidden');
            } else {
                checkOutTabBtn.classList.add('border-blue-500', 'text-white');
                checkOutTabBtn.classList.remove('border-transparent', 'text-[#9CA3AF]');
                checkInTabBtn.classList.add('border-transparent', 'text-[#9CA3AF]');
                checkInTabBtn.classList.remove('border-blue-500', 'text-white');
                
                checkOutTab.classList.remove('hidden');
                checkInTab.classList.add('hidden');
                
                // Load checked-in guests when switching to checkout tab
                loadCheckedInGuests();
            }
        }
        
        // Add event listeners for tab switching
        checkInTabBtn.addEventListener('click', () => switchToTab('checkIn'));
        checkOutTabBtn.addEventListener('click', () => switchToTab('checkOut'));
        
        // Automatically refresh the guest list after a successful check-in
        function refreshGuestListAfterCheckIn() {
            // Switch to the checkout tab to show the updated list
            switchToTab('checkOut');
        }

        // Load checked-in guests
        function loadCheckedInGuests() {
            fetch('{{ route("admin.session.guests") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.guests.length > 0) {
                        checkedInGuestsList.innerHTML = '';
                        noGuestsMessage.classList.add('hidden');
                        
                        data.guests.forEach(guest => {
                            // Calculate how long ago guest checked in
                            const checkinTime = new Date(guest.time);
                            const now = new Date();
                            const diffMs = now - checkinTime;
                            const diffMins = Math.floor(diffMs / 60000);
                            let timeAgo;
                            
                            if (diffMins < 1) {
                                timeAgo = 'just now';
                            } else if (diffMins < 60) {
                                timeAgo = `${diffMins} min${diffMins > 1 ? 's' : ''} ago`;
                            } else {
                                const diffHrs = Math.floor(diffMins / 60);
                                timeAgo = `${diffHrs} hr${diffHrs > 1 ? 's' : ''} ago`;
                            }
                            
                            const guestItem = document.createElement('div');
                            guestItem.className = 'flex items-center justify-between p-3 border-b border-[#374151] last:border-0';
                            guestItem.innerHTML = `
                                <div class="flex items-center">
                                    <div class="h-9 w-9 rounded-full bg-gray-600 flex items-center justify-center text-white font-bold text-xs mr-3">
                                        ${guest.guest_name.substring(0, 2).toUpperCase()}
                                    </div>
                                    <div>
                                        <p class="text-white text-sm font-medium">${guest.guest_name}</p>
                                        <p class="text-[#9CA3AF] text-xs">Checked in ${timeAgo}</p>
                                    </div>
                                </div>
                                <button class="checkout-guest-btn p-2 bg-red-600 hover:bg-red-700 text-white rounded-md" 
                                    data-id="${guest.id}" 
                                    data-name="${guest.guest_name}" 
                                    data-phone="${guest.mobile_number}">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                            `;
                            
                            checkedInGuestsList.appendChild(guestItem);
                        });
                        
                        // Add event listeners to the checkout buttons
                        document.querySelectorAll('.checkout-guest-btn').forEach(button => {
                            button.addEventListener('click', function() {
                                const guestId = this.getAttribute('data-id');
                                const guestName = this.getAttribute('data-name');
                                const guestPhone = this.getAttribute('data-phone');
                                handleGuestCheckout(guestId, guestName, guestPhone);
                            });
                        });
                    } else {
                        noGuestsMessage.innerHTML = '<i class="fas fa-info-circle mr-2"></i> No guests currently checked in.';
                        noGuestsMessage.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error fetching guests:', error);
                    noGuestsMessage.innerHTML = '<i class="fas fa-exclamation-circle mr-2 text-red-500"></i> Error loading guests. Please try again.';
                    noGuestsMessage.classList.remove('hidden');
                });
        }
        
        // Function to handle guest checkout
        function handleGuestCheckout(guestId, guestName, guestPhone) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Confirm Check-Out',
                text: `Are you sure you want to check out ${guestName}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#4B5563',
                confirmButtonText: 'Yes, Check Out'
            }).then((result) => {
                if (result.isConfirmed) {
                    processGuestCheckout(guestId, guestName, guestPhone);
                }
            });
        }
        
        // Process the guest checkout
        function processGuestCheckout(guestId, guestName, guestPhone) {
            // Show processing message
            Swal.fire({
                title: 'Processing...',
                text: `Processing guest check-out`,
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send data to server - use session_id to properly update in database
            const formData = new FormData();
            formData.append('session_id', guestId); // This is the key change - send session_id to mark specific session as OUT
            formData.append('status', 'OUT');
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route("admin.session.store") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: `${guestName} has been checked out successfully!`,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Remove the guest from the checked-in list immediately
                        const guestItem = document.querySelector(`button[data-id="${guestId}"]`).closest('div.flex.items-center.justify-between');
                        if (guestItem) {
                            guestItem.remove();
                            
                            // Check if there are any guests left
                            if (checkedInGuestsList.querySelectorAll('div.flex.items-center.justify-between').length === 0) {
                                noGuestsMessage.innerHTML = '<i class="fas fa-info-circle mr-2"></i> No guests currently checked in.';
                                noGuestsMessage.classList.remove('hidden');
                            }
                        }
                        
                        // Add to the session table without refreshing the page
                        const tbody = document.querySelector('#sessionTable tbody');
                        const newRow = document.createElement('tr');
                        newRow.className = 'hover:bg-[#374151] transition-colors';
                        
                        const date = new Date();
                        const options = { timeZone: 'Asia/Manila', month: 'short', day: 'numeric', year: 'numeric' };
                        const timeOptions = { timeZone: 'Asia/Manila', hour: 'numeric', minute: 'numeric', hour12: true };
                        const formattedDate = date.toLocaleDateString('en-US', options);
                        const formattedTime = date.toLocaleTimeString('en-US', timeOptions);
                        
                        newRow.innerHTML = `
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 rounded-full bg-gray-600 flex items-center justify-center text-white font-bold text-xs">
                                        ${guestName.substring(0, 2).toUpperCase()}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-medium text-white">${guestName}</td>
                            <td class="px-4 py-3 text-[#9CA3AF] capitalize">guest</td>
                            <td class="px-4 py-3 text-[#9CA3AF]">${formattedDate}</td>
                            <td class="px-4 py-3 text-[#9CA3AF]">${formattedTime}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full bg-red-500 text-white">
                                    <i class="fas fa-arrow-left text-xs"></i>
                                    OUT
                                </span>
                            </td>
                        `;
                        
                        if (tbody.firstChild) {
                            tbody.insertBefore(newRow, tbody.firstChild);
                        } else {
                            tbody.appendChild(newRow);
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.error || 'Error processing guest check-out',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Network error. Please check your connection and try again.',
                    icon: 'error'
                });
            });
        }

        // Show guest modal
        guestButton.addEventListener('click', function() {
            guestModal.classList.remove('hidden');
            switchToTab('checkIn'); // Default to check-in tab
            guestNameInput.focus(); // Focus the name input
        });

        // Close guest modal function
        function closeGuestModalFunction() {
            guestModal.classList.add('hidden');
            guestNameInput.value = ''; // Clear input on close
            guestNameError.classList.add('hidden'); // Hide error on close
            guestPhoneInput.value = ''; // Clear phone input on close
            guestPhoneError.classList.add('hidden'); // Hide phone error on close
        }

        // Event listeners for closing guest modal
        closeGuestModal.addEventListener('click', closeGuestModalFunction);
        cancelGuestModal.addEventListener('click', closeGuestModalFunction);
        guestModalOverlay.addEventListener('click', function(event) {
            if (event.target === guestModalOverlay) {
                closeGuestModalFunction();
            }
        });

        // Function to validate Philippine phone number
        function isValidPhoneNumber(phone) {
            // Basic regex for Philippine phone numbers: +63XXXXXXXXXX or 09XXXXXXXXXX
            const philippinePhoneRegex = /^(\+63|0)9\d{9}$/;
            return philippinePhoneRegex.test(phone.replace(/\s+/g, ''));
        }

        // Function to handle guest check-in submission
        function handleGuestSubmit(status) {
            const guestName = guestNameInput.value.trim();
            const guestPhone = guestPhoneInput.value.trim();
            let isValid = true;
            
            // Validate name
            if (!guestName) {
                guestNameError.classList.remove('hidden');
                guestNameInput.focus();
                isValid = false;
            } else {
                guestNameError.classList.add('hidden');
            }
            
            // Validate phone number
            if (!guestPhone || !isValidPhoneNumber(guestPhone)) {
                guestPhoneError.classList.remove('hidden');
                if (isValid) {
                    guestPhoneInput.focus();
                }
                isValid = false;
            } else {
                guestPhoneError.classList.add('hidden');
            }
            
            // If validation fails, stop here
            if (!isValid) {
                return;
            }

            // Show processing alert
            Swal.fire({
                title: 'Processing...',
                text: `Processing guest check-in`,
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send data to server
            const formData = new FormData();
            formData.append('guest_name', guestName);
            formData.append('mobile_number', guestPhone);
            formData.append('status', 'IN'); // Always IN for the check in tab
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('timezone', 'Asia/Manila'); // Add Philippines timezone
            
            fetch('{{ route("admin.session.store") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json().catch(err => {
                    console.error('JSON parse error:', err);
                    throw new Error('Invalid JSON response');
                });
            })
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    // Reuse the existing success handler
                    handleSuccessfulScan(data.data);
                    
                    // Clear the form
                    guestNameInput.value = '';
                    guestPhoneInput.value = '';
                    
                    // Refresh the guest list and switch to checkout tab after a short delay
                    setTimeout(refreshGuestListAfterCheckIn, 1000);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.error || 'Error processing guest request',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Network error. Please check your connection and try again.',
                    icon: 'error'
                });
            });
        }

        // Event listener for guest check-in button
        guestCheckInBtn.addEventListener('click', () => handleGuestSubmit('IN'));
    });
</script>

<style>
    @keyframes scanline {
        0% {
            transform: translateY(-100%);
        }
        100% {
            transform: translateY(1000%);
        }
    }
    
    /* Scanner video and container styles */
    #scanner-container {
        min-height: 300px;
        background-color: #000;
        position: relative;
    }
    
    #scanner-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    
    #scanner-canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    /* Mode selection button styles */
    #timeInBtn, #timeOutBtn {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    #timeInBtn.ring-2, #timeOutBtn.ring-2 {
        transform: scale(1.05);
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
    }
    
    #timeInBtn.ring-2::after, #timeOutBtn.ring-2::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 10%;
        width: 80%;
        height: 3px;
        background-color: white;
        border-radius: 3px;
    }
    
    /* Pulse animation for active button */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
    }
    
    #timeInBtn.ring-2, #timeOutBtn.ring-2 {
        animation: pulse 2s infinite;
    }
    
    /* Fix for button layering issues */
    .checkout-guest-btn {
        position: relative;
        z-index: 50 !important;
        isolation: isolate;
    }
    
    #checkInTabBtn, #checkOutTabBtn, #guestCheckInBtn, #cancelGuestModal, #closeGuestModal {
        position: relative;
        z-index: 50 !important;
    }
    
    #guestModal .z-30, #guestModal .z-20, #guestModal .z-10 {
        isolation: isolate;
    }
    
    #checkedInGuestsList {
        isolation: isolate;
    }
</style>
@endsection
