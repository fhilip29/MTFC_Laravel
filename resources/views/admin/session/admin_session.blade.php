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

        <input 
            type="text" 
            id="searchInput" 
            placeholder="Search by name or date..." 
            class="w-full p-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] shadow-sm mb-4"
        >

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
                                            <div class="h-9 w-9 rounded-full {{ $roleBgColor }} flex items-center justify-center text-white font-bold text-xs">
                                                {{ $session->user ? strtoupper(substr($session->user->full_name, 0, 2)) : 'G' }} 
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-white">{{ $session->user->full_name ?? 'Guest User' }}</td>
                                    <td class="px-4 py-3 text-[#9CA3AF] capitalize">{{ $session->user->role ?? 'guest' }}</td> 
                                    <td class="px-4 py-3 text-[#9CA3AF]">{{ \Carbon\Carbon::parse($session->time)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-[#9CA3AF]">{{ \Carbon\Carbon::parse($session->time)->format('h:i A') }}</td>
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
                        <h3 class="text-lg leading-6 font-medium text-white flex items-center justify-between relative z-10">
                            <span>Guest Check-in/out</span>
                            <button id="closeGuestModal" class="text-[#9CA3AF] hover:text-white transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </h3>
                        <div class="mt-4 relative z-10">
                            <label for="guestNameInput" class="block text-sm font-medium text-[#9CA3AF] mb-1">Guest Name</label>
                            <input type="text" id="guestNameInput" placeholder="Enter guest's full name" class="w-full p-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] shadow-sm mb-4">
                            <p id="guestNameError" class="text-red-500 text-xs mt-1 hidden">Guest name is required.</p>
                        </div>
                        <div class="mt-4 mb-6 flex gap-4 justify-center relative z-10">
                            <button id="guestCheckInBtn" class="py-3 px-5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md shadow flex items-center gap-2 transition-colors flex-1 justify-center">
                                <i class="fas fa-sign-in-alt text-lg"></i>
                                <span class="text-md font-bold">Check In Guest</span>
                            </button>
                            <button id="guestCheckOutBtn" class="py-3 px-5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md shadow flex items-center gap-2 transition-colors flex-1 justify-center">
                                <i class="fas fa-sign-out-alt text-lg"></i>
                                <span class="text-md font-bold">Check Out Guest</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-[#111827] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse relative z-10">
                <button id="cancelGuestModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-[#374151] shadow-sm px-4 py-2 bg-[#1F2937] text-base font-medium text-[#9CA3AF] hover:bg-[#374151] focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
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
    // Simple search filter
    document.getElementById('searchInput').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#sessionTable tbody tr');

        rows.forEach(row => {
            // Improved search: Check Name and Role columns
            let nameCell = row.cells[1];
            let roleCell = row.cells[2];
            let text = (nameCell ? nameCell.innerText.toLowerCase() : '') + ' ' + 
                       (roleCell ? roleCell.innerText.toLowerCase() : '');
            row.style.display = text.includes(filter) ? '' : 'none';
        });
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
        const guestCheckInBtn = document.getElementById('guestCheckInBtn');
        const guestCheckOutBtn = document.getElementById('guestCheckOutBtn');
        
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
            
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                // Try to get a list of available cameras first
                navigator.mediaDevices.enumerateDevices()
                    .then(devices => {
                        const videoDevices = devices.filter(device => device.kind === 'videoinput');
                        console.log('Available cameras:', videoDevices);
                        
                        if (videoDevices.length === 0) {
                            scannerMessage.textContent = 'No camera detected on this device.';
                            scannerMessage.classList.add('text-red-500');
                            return;
                        }
                        
                        // Proceed with camera access
                        return navigator.mediaDevices.getUserMedia({ 
                            video: { 
                                // Try to use any available camera
                                deviceId: videoDevices.length > 0 ? {exact: videoDevices[0].deviceId} : undefined,
                                width: { ideal: 640 },
                                height: { ideal: 480 }
                            },
                            audio: false
                        });
                    })
                    .then(function(mediaStream) {
                        if (!mediaStream) return; // Handle case where we didn't get a stream
                        
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
                            errorMessage += 'Camera permission denied. Please allow camera access in your browser settings.';
                        } else if (error.name === 'NotFoundError') {
                            errorMessage += 'No camera found on this device.';
                        } else if (error.name === 'NotReadableError') {
                            errorMessage += 'Camera is already in use by another application.';
                        } else {
                            errorMessage += error.message || 'Unknown error.';
                        }
                        
                        scannerMessage.textContent = errorMessage;
                        scannerMessage.classList.add('text-red-500');
                    });
            } else {
                scannerMessage.textContent = 'Camera access not supported in this browser.';
                scannerMessage.classList.add('text-red-500');
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
                    const beep = new Audio('data:audio/wav;base64,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');
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
                    status: scanMode
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
            
            const date = new Date(data.time);
            const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            const formattedTime = date.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
            const initials = data.full_name ? data.full_name.substring(0, 2).toUpperCase() : 'G';
            const userRole = data.role || 'guest'; // Get role from response
            
            // Determine avatar color for new row
            let newRowRoleBgColor = 'bg-gray-600';
            switch (userRole) {
                case 'member': newRowRoleBgColor = 'bg-blue-600'; break;
                case 'trainer': newRowRoleBgColor = 'bg-purple-600'; break;
                case 'admin': newRowRoleBgColor = 'bg-yellow-600'; break;
            }

            newRow.innerHTML = `
                <td class="px-4 py-3">
                    <div class="flex items-center">
                        <div class="h-9 w-9 rounded-full ${newRowRoleBgColor} flex items-center justify-center text-white font-bold text-xs">
                            ${initials}
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 font-medium text-white">${data.full_name || 'Guest User'}</td>
                <td class="px-4 py-3 text-[#9CA3AF] capitalize">${userRole}</td>
                <td class="px-4 py-3 text-[#9CA3AF]">${formattedDate}</td>
                <td class="px-4 py-3 text-[#9CA3AF]">${formattedTime}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full ${data.status === 'IN' ? 'bg-green-500' : 'bg-red-500'} text-white">
                        ${data.status === 'IN' ? '<i class="fas fa-arrow-right text-xs"></i>' : '<i class="fas fa-arrow-left text-xs"></i>'}
                        ${data.status}
                    </span>
                </td>
            `;
            
            // Insert the new row at the top of the table
            if (tbody.firstChild) {
                tbody.insertBefore(newRow, tbody.firstChild);
            } else {
                tbody.appendChild(newRow);
            }
            
            // Reset guest modal input if it was used
            if (guestNameInput) {
                 guestNameInput.value = '';
                 guestNameError.classList.add('hidden');
            }
           
            // Close the modal after a brief delay (applies to both scanner and guest modals)
            // Use separate close functions if needed, but this might suffice
            if(scannerModal.classList.contains('hidden') === false) {
                setTimeout(closeModal, 1500);
            }
            if(guestModal.classList.contains('hidden') === false) {
                setTimeout(closeGuestModalFunction, 1500);
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

        // Show guest modal
        guestButton.addEventListener('click', function() {
            guestModal.classList.remove('hidden');
            guestNameInput.focus(); // Focus the name input
        });

        // Close guest modal function
        function closeGuestModalFunction() {
            guestModal.classList.add('hidden');
            guestNameInput.value = ''; // Clear input on close
            guestNameError.classList.add('hidden'); // Hide error on close
        }

        // Event listeners for closing guest modal
        closeGuestModal.addEventListener('click', closeGuestModalFunction);
        cancelGuestModal.addEventListener('click', closeGuestModalFunction);
        guestModalOverlay.addEventListener('click', function(event) {
            if (event.target === guestModalOverlay) {
                closeGuestModalFunction();
            }
        });

        // Function to handle guest check-in/out submission
        function handleGuestSubmit(status) {
            const guestName = guestNameInput.value.trim();
            
            // Validation
            if (!guestName) {
                guestNameError.classList.remove('hidden');
                guestNameInput.focus();
                return;
            } else {
                guestNameError.classList.add('hidden');
            }

            // Show processing alert
            Swal.fire({
                title: 'Processing...',
                text: `Processing guest ${status === 'IN' ? 'check-in' : 'check-out'}`,
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send data to server
            fetch('{{ route("admin.session.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                },
                body: JSON.stringify({
                    guest_name: guestName,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    // Reuse the existing success handler
                    handleSuccessfulScan(data.data);
                    // Close modal is handled within handleSuccessfulScan now
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

        // Event listeners for guest action buttons
        guestCheckInBtn.addEventListener('click', () => handleGuestSubmit('IN'));
        guestCheckOutBtn.addEventListener('click', () => handleGuestSubmit('OUT'));

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
</style>
@endsection
