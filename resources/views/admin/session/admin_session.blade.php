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
                <button class="w-full sm:w-auto bg-[#374151] hover:bg-[#4B5563] text-white px-4 py-2 rounded-md shadow-md flex items-center justify-center gap-2 transition-colors">
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
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Time</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#374151]">
                            @php
                                $sessions = [
                                    ['id' => '67dfbb6ea96c19be54fae25b', 'name' => 'King Dranreb Languido', 'time' => '2025-04-05T15:30:15.000Z', 'status' => 'OUT'],
                                    ['id' => '67dfbb6ea96c19be54fae25b', 'name' => 'King Dranreb Languido', 'time' => '2025-04-05T15:29:58.195Z', 'status' => 'IN'],
                                    ['id' => '67dfbb6ea96c19be54fae25b', 'name' => 'King Dranreb Languido', 'time' => '2025-04-04T06:11:14.000Z', 'status' => 'OUT'],
                                    ['id' => '67ed927ddd2e9713ad201512', 'name' => 'Tester Tester', 'time' => '2025-04-02T20:00:35.000Z', 'status' => 'OUT'],
                                    ['id' => '67ed927ddd2e9713ad201512', 'name' => 'Tester Tester', 'time' => '2025-04-02T19:59:51.889Z', 'status' => 'IN'],
                                    ['id' => '67dfbb6ea96c19be54fae25b', 'name' => 'King Dranreb Languido', 'time' => '2025-03-31T15:24:06.000Z', 'status' => 'OUT'],
                                ];
                            @endphp

                            @foreach ($sessions as $session)
                                <tr class="hover:bg-[#374151] transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="h-9 w-9 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-xs">
                                                {{ strtoupper(substr($session['name'], 0, 2)) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-white">{{ $session['name'] }}</td>
                                    <td class="px-4 py-3 text-[#9CA3AF]">{{ \Carbon\Carbon::parse($session['time'])->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-[#9CA3AF]">{{ \Carbon\Carbon::parse($session['time'])->format('h:i A') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $session['status'] === 'IN' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                            {{ $session['status'] }}
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
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-black opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-[#1F2937] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[#374151]">
            <div class="bg-[#1F2937] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-white flex items-center justify-between">
                            <span>QR Code Scanner</span>
                            <button id="closeScanner" class="text-[#9CA3AF] hover:text-white transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </h3>
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
                            <div id="scanner-message" class="mt-4 text-center text-white">Position the QR code within the frame</div>
                            
                            <!-- Time In/Out Selection -->
                            <div class="flex gap-3 mt-4 justify-center">
                                <button id="timeInBtn" class="py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md shadow active:bg-green-800 flex items-center gap-2 transition-colors">
                                    <i class="fas fa-sign-in-alt"></i> Time In
                                </button>
                                <button id="timeOutBtn" class="py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md shadow active:bg-red-800 flex items-center gap-2 transition-colors">
                                    <i class="fas fa-sign-out-alt"></i> Time Out
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-[#111827] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
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

<!-- Import jsQR library -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<script>
    // Simple search filter
    document.getElementById('searchInput').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#sessionTable tbody tr');

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
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
        
        let scanning = false;
        let stream = null;
        let scanMode = 'IN'; // Default scan mode
        let canvasContext = canvas.getContext('2d');
        
        // Set active scan mode
        function setActiveScanMode(mode) {
            scanMode = mode;
            
            if (mode === 'IN') {
                timeInBtn.classList.add('ring-2', 'ring-white');
                timeOutBtn.classList.remove('ring-2', 'ring-white');
            } else {
                timeOutBtn.classList.add('ring-2', 'ring-white');
                timeInBtn.classList.remove('ring-2', 'ring-white');
            }
            
            scannerMessage.textContent = `Ready to scan for Time ${mode}`;
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
        });
        
        // Close scanner modal
        function closeModal() {
            scannerModal.classList.add('hidden');
            stopScanner();
        }
        
        closeScanner.addEventListener('click', closeModal);
        cancelScanner.addEventListener('click', closeModal);
        
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
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                    .then(function(mediaStream) {
                        stream = mediaStream;
                        video.srcObject = mediaStream;
                        video.setAttribute('playsinline', true);
                        video.play();
                        scanning = true;
                        startScanAnimation();
                        scannerMessage.textContent = `Scanning for Time ${scanMode}...`;
                        
                        // Set canvas size to match video
                        video.addEventListener('loadedmetadata', function() {
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                        });
                        
                        // Start QR code detection loop
                        requestAnimationFrame(scanQRCode);
                    })
                    .catch(function(error) {
                        console.error('Error accessing camera:', error);
                        scannerMessage.textContent = 'Error accessing camera. Please check permissions.';
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
                if (data.success) {
                    handleSuccessfulScan(data.data);
                } else {
                    scannerMessage.textContent = data.error || 'Error processing QR code';
                    scannerMessage.classList.add('text-red-500');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                scannerMessage.textContent = 'Error processing scan. Please try again.';
                scannerMessage.classList.add('text-red-500');
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
            
            // Add the new session to the table
            const tbody = document.querySelector('#sessionTable tbody');
            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-[#374151] transition-colors';
            
            const date = new Date(data.time);
            const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            const formattedTime = date.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
            const initials = data.full_name.substring(0, 2).toUpperCase();
            
            newRow.innerHTML = `
                <td class="px-4 py-3">
                    <div class="flex items-center">
                        <div class="h-9 w-9 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-xs">
                            ${initials}
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 font-medium text-white">${data.full_name}</td>
                <td class="px-4 py-3 text-[#9CA3AF]">${formattedDate}</td>
                <td class="px-4 py-3 text-[#9CA3AF]">${formattedTime}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${data.status === 'IN' ? 'bg-green-500' : 'bg-red-500'} text-white">${data.status}</span>
                </td>
            `;
            
            // Insert the new row at the top of the table
            if (tbody.firstChild) {
                tbody.insertBefore(newRow, tbody.firstChild);
            } else {
                tbody.appendChild(newRow);
            }
            
            // Close the modal after a brief delay
            setTimeout(closeModal, 1500);
        }
        
        // Scan animation
        function startScanAnimation() {
            scanAnimation.style.animation = 'scanline 2s linear infinite';
        }
        
        function stopScanAnimation() {
            scanAnimation.style.animation = 'none';
        }
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
</style>
@endsection
