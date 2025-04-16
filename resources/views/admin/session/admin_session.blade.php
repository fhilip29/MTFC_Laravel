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
            placeholder="Search by client ID or name..." 
            class="w-full p-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] shadow-sm mb-4"
        >

        <div class="-mx-4 sm:mx-0 overflow-x-auto bg-[#1F2937] rounded-lg shadow-md">
            <div class="min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-[#374151] text-sm text-left" id="sessionTable">
                        <thead class="bg-[#374151] text-[#9CA3AF] uppercase sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3">Client ID</th>
                                <th class="px-4 py-3">Full Name</th>
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
                                    <td class="px-4 py-3 font-mono text-white">{{ $session['id'] }}</td>
                                    <td class="px-4 py-3 font-medium text-white">{{ $session['name'] }}</td>
                                    <td class="px-4 py-3 text-[#9CA3AF]">{{ \Carbon\Carbon::parse($session['time'])->format('M d, Y h:i A') }}</td>
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
        const video = document.getElementById('scanner-video');
        const scanAnimation = document.getElementById('scan-animation');
        const scannerMessage = document.getElementById('scanner-message');
        
        let scanning = false;
        let stream = null;
        
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
                        scannerMessage.textContent = 'Scanning...';
                        
                        // Simulate QR code detection (in a real app, you'd use a library like jsQR)
                        setTimeout(function() {
                            if (scanning) {
                                handleSuccessfulScan('67dfbb6ea96c19be54fae25b');
                            }
                        }, 5000);
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
        
        // Stop scanner function
        function stopScanner() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
                stream = null;
            }
            scanning = false;
            stopScanAnimation();
            scannerMessage.textContent = 'Position the QR code within the frame';
            scannerMessage.classList.remove('text-red-500', 'text-green-500');
        }
        
        // Handle successful scan
        function handleSuccessfulScan(memberId) {
            stopScanAnimation();
            scannerMessage.textContent = 'Member ID: ' + memberId + ' - Successfully scanned!';
            scannerMessage.classList.add('text-green-500');
            scanning = false;
            startScannerBtn.textContent = 'Start Scanner';
            startScannerBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
            startScannerBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            
            // In a real application, you would send this ID to the server
            // and update the session table with the new check-in/out
            console.log('Member scanned:', memberId);
            
            // Simulate adding a new row to the table
            setTimeout(function() {
                const tbody = document.querySelector('#sessionTable tbody');
                const newRow = document.createElement('tr');
                newRow.className = 'hover:bg-[#374151] transition-colors';
                
                const now = new Date();
                const formattedDate = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + 
                                     ' ' + now.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
                
                newRow.innerHTML = `
                    <td class="px-4 py-3 font-mono text-white">${memberId}</td>
                    <td class="px-4 py-3 font-medium text-white">King Dranreb Languido</td>
                    <td class="px-4 py-3 text-[#9CA3AF]">${formattedDate}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-500 text-white">IN</span>
                    </td>
                `;
                
                tbody.insertBefore(newRow, tbody.firstChild);
                closeModal();
            }, 1500);
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
