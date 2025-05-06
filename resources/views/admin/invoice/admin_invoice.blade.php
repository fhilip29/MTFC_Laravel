@extends('layouts.admin')

@section('title', 'Manage Invoices')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <div class="bg-[#1F2937] shadow-lg rounded-xl p-4 sm:p-6 border border-[#374151]">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-6 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2 w-full sm:w-auto">
                <i class="fas fa-file-invoice text-[#9CA3AF]"></i> Manage Invoices
            </h1>
            <div class="flex gap-4 w-full sm:w-auto">
                <a href="{{ route('admin.invoice.export') }}" class="bg-[#374151] hover:bg-[#4B5563] text-white font-semibold flex items-center gap-2 px-4 py-2 rounded-lg shadow transition-colors w-full sm:w-auto justify-center">
                    <i class="fas fa-file-export"></i> <span class="sm:inline">Export</span>
                </a>
                <button onclick="openScanner()" class="bg-[#374151] hover:bg-[#4B5563] text-white font-semibold flex items-center gap-2 px-4 py-2 rounded-lg shadow transition-colors w-full sm:w-auto justify-center">
                    <i class="fas fa-qrcode"></i> <span class="sm:inline">Scan Payment</span>
                </button>
            </div>
        </div>

        <div class="mb-6 flex flex-col sm:flex-row justify-between gap-4 items-center">
            <div class="relative w-full sm:w-1/3">
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Search Invoice..." 
                    class="w-full pl-10 pr-4 py-2 bg-[#374151] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] text-sm sm:text-base"
                >
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]"></i>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <select id="filterType" class="bg-[#374151] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] text-sm sm:text-base px-3 py-2">
                    <option value="">All Types</option>
                    <option value="product">Products</option>
                    <option value="subscription">Subscriptions</option>
                </select>
                <input 
                    type="date" 
                    id="dateFilter"
                    class="bg-[#374151] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] text-sm sm:text-base px-3 py-2"
                >
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-sm -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
            <table class="min-w-full divide-y divide-[#374151] text-xs sm:text-sm text-left" id="invoiceTable">
                <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-xs sticky top-0 z-10">
                    <tr>
                        <th class="px-3 sm:px-4 py-3">Invoice Number</th>
                        <th class="px-3 sm:px-4 py-3">Client</th>
                        <th class="px-3 sm:px-4 py-3">Type</th>
                        <th class="px-3 sm:px-4 py-3">Amount</th>
                        <th class="px-3 sm:px-4 py-3">Date</th>
                        <th class="px-3 sm:px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#374151]">
                    @forelse ($invoices as $invoice)
                        <tr class="hover:bg-[#374151] transition-colors">
                            <td class="px-3 sm:px-4 py-3 font-mono text-white text-xs sm:text-sm">{{ $invoice->invoice_number }}</td>
                            <td class="px-3 sm:px-4 py-3 font-medium text-white text-xs sm:text-sm">
                                {{ $invoice->user ? $invoice->user->full_name : 'WALKIN-GUEST' }}
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm">
                                <span class="px-2 py-1 rounded-full text-xs {{ $invoice->type === 'subscription' ? 'bg-blue-900 text-blue-200' : 'bg-green-900 text-green-200' }}">
                                    {{ ucfirst($invoice->type) }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-white text-xs sm:text-sm">₱{{ number_format($invoice->total_amount, 2) }}</td>
                            <td class="px-3 sm:px-4 py-3 text-[#9CA3AF] text-xs sm:text-sm">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</td>
                            <td class="px-3 sm:px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a 
                                        href="{{ route('admin.invoice.show', $invoice->id) }}" 
                                        class="inline-flex items-center gap-1 sm:gap-2 text-[#9CA3AF] hover:text-white font-medium transition-colors text-xs sm:text-sm"
                                        title="View Invoice"
                                    >
                                        <i class="fas fa-eye"></i> <span class="hidden sm:inline">View</span>
                                    </a>
                                    <a 
                                        href="{{ route('admin.invoice.print', $invoice->id) }}" 
                                        class="inline-flex items-center gap-1 sm:gap-2 text-[#9CA3AF] hover:text-white font-medium transition-colors text-xs sm:text-sm"
                                        title="Print Receipt"
                                    >
                                        <i class="fas fa-print"></i> <span class="hidden sm:inline">Print</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 sm:px-4 py-6 text-center text-[#9CA3AF]">
                                No invoices found. All transactions will appear here.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>

<!-- Payment Scanner Modal -->
<div id="scannerModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeScanner()"></div>
        
        <div class="relative bg-[#1F2937] rounded-lg max-w-md w-full mx-auto shadow-xl z-50 border border-[#374151]">
            <div class="flex items-center justify-between p-4 border-b border-[#374151]">
                <h3 class="text-xl font-bold text-white">Scan Payment QR Code</h3>
                <button onclick="closeScanner()" class="text-[#9CA3AF] hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6">
                <!-- Success message (initially hidden) -->
                <div id="payment-success-message" class="hidden bg-green-600 text-white p-4 rounded-lg mb-4 animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-2xl mr-2"></i>
                        <div>
                            <h4 class="font-bold">Payment Confirmed!</h4>
                            <p class="text-sm" id="payment-success-details"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Error message (initially hidden) -->
                <div id="payment-error-message" class="hidden bg-red-600 text-white p-4 rounded-lg mb-4 animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-2xl mr-2"></i>
                        <div>
                            <h4 class="font-bold">Payment Error</h4>
                            <p class="text-sm" id="payment-error-details"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Scanner container -->
                <div class="relative mb-4">
                    <video id="scanner" class="w-full h-96 bg-black rounded-lg"></video>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-64 h-64 border-2 border-white rounded-lg"></div>
                    </div>
                </div>
                
                <!-- Scanner controls - moved outside the scanner frame -->
                <div class="flex justify-center space-x-3 mb-4">
                    <button id="startScanner" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center">
                        <i class="fas fa-play mr-2"></i> Start Scanner
                    </button>
                    <button id="switchCamera" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i> Switch Camera
                    </button>
                    <button id="toggleFrontCamera" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center">
                        <i class="fas fa-camera mr-2"></i> Front Camera
                    </button>
                </div>
                
                <!-- Scanner message -->
                <div id="scanner-message" class="text-[#9CA3AF] text-sm text-center mt-2">
                    Position the QR code within the frame
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include QR Scanner Library -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    let html5QrcodeScanner = null;
    let currentCameraId = null;
    let availableCameras = [];
    let isFrontCamera = false;
    
    // Initialize buttons on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initially hide the switch camera button until we know there are multiple cameras
        document.getElementById('switchCamera').style.display = 'none';
        
        // Add event listeners to buttons
        document.getElementById('startScanner').addEventListener('click', function() {
            startScanner();
        });
        
        document.getElementById('switchCamera').addEventListener('click', function() {
            switchCamera();
        });

        document.getElementById('toggleFrontCamera').addEventListener('click', function() {
            isFrontCamera = !isFrontCamera;
            this.classList.toggle('bg-purple-600');
            this.classList.toggle('bg-purple-800');
            if (html5QrcodeScanner) {
                startScanner();
            }
        });
    });
    
    function openScanner() {
        document.getElementById('scannerModal').classList.remove('hidden');
        // Show the start button by default
        document.getElementById('startScanner').style.display = 'flex';
    }
    
    function closeScanner() {
        document.getElementById('scannerModal').classList.add('hidden');
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop();
        }
    }
    
    async function startScanner() {
        try {
            // Get available cameras
            const devices = await Html5Qrcode.getCameras();
            availableCameras = devices;
            
            if (availableCameras.length === 0) {
                showError('No cameras found');
                return;
            }

            // Log available cameras for debugging
            console.log("Available cameras:", availableCameras);

            // Filter cameras based on front/back preference
            let filteredCameras = availableCameras.filter(camera => {
                const label = (camera.label || '').toLowerCase();
                if (isFrontCamera) {
                    return label.includes('front') || label.includes('user') || label.includes('webcam');
                } else {
                    return label.includes('back') || label.includes('environment') || label.includes('rear');
                }
            });

            // If no cameras match our filter, use all available cameras
            if (filteredCameras.length === 0) {
                console.log("No matching cameras found, using all available cameras");
                filteredCameras = availableCameras;
                showMessage(`Using all available cameras`);
            }
            
            // Use the first camera by default or continue with current if switching
            if (!currentCameraId || !filteredCameras.find(cam => cam.id === currentCameraId)) {
                currentCameraId = filteredCameras[0].id;
            }
            
            // Show switch camera button only if multiple cameras are available
            document.getElementById('switchCamera').style.display = 
                filteredCameras.length > 1 ? 'flex' : 'none';
            
            // Initialize scanner
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop();
            }

            console.log(`Using camera with ID: ${currentCameraId}`);
            
            // Create new scanner instance with HTML5QrcodeScanner
            html5QrcodeScanner = new Html5Qrcode("scanner");
            
            const qrConfig = {
                fps: 10,
                qrbox: { width: 300, height: 300 },
                aspectRatio: 1.0
            };
            
            await html5QrcodeScanner.start(
                { deviceId: { exact: currentCameraId } },
                qrConfig,
                onScanSuccess,
                onScanFailure
            );
            
            showMessage(`Scanner started with camera: ${filteredCameras.find(cam => cam.id === currentCameraId)?.label || 'Unknown'}`);
            
        } catch (error) {
            showError('Failed to start scanner: ' + error.message);
            console.error('Error starting scanner:', error);
            
            // Try with default camera as fallback
            try {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.stop();
                }
                
                html5QrcodeScanner = new Html5Qrcode("scanner");
                
                const qrConfig = {
                    fps: 10,
                    qrbox: { width: 300, height: 300 }
                };
                
                await html5QrcodeScanner.start(
                    { facingMode: isFrontCamera ? "user" : "environment" },
                    qrConfig,
                    onScanSuccess,
                    onScanFailure
                );
                
                showMessage("Scanner started with fallback method");
                
            } catch (fallbackError) {
                console.error('Fallback error:', fallbackError);
                showError('Scanner initialization failed. Please try a different browser or device.');
            }
        }
    }
    
    function switchCamera() {
        if (availableCameras.length <= 1) {
            showError('No additional cameras available');
            return;
        }
        
        // Filter cameras based on front/back preference
        let filteredCameras = availableCameras.filter(camera => {
            const label = (camera.label || '').toLowerCase();
            if (isFrontCamera) {
                return label.includes('front') || label.includes('user') || label.includes('webcam');
            } else {
                return label.includes('back') || label.includes('environment') || label.includes('rear');
            }
        });

        // If no cameras match our filter, use all available cameras
        if (filteredCameras.length === 0) {
            filteredCameras = availableCameras;
        }

        if (filteredCameras.length <= 1) {
            showError('No additional cameras available');
            return;
        }
        
        // Find current camera index
        const currentIndex = filteredCameras.findIndex(camera => camera.id === currentCameraId);
        // Switch to next camera
        const nextIndex = (currentIndex + 1) % filteredCameras.length;
        currentCameraId = filteredCameras[nextIndex].id;
        
        // Show which camera is being used
        showMessage(`Switched to camera ${nextIndex + 1}/${filteredCameras.length}: ${filteredCameras[nextIndex].label || 'Unknown'}`);
        
        // Restart scanner with new camera
        startScanner();
    }
    
    function showMessage(message) {
        const messageElement = document.getElementById('scanner-message');
        messageElement.textContent = message;
        messageElement.classList.remove('text-[#9CA3AF]');
        messageElement.classList.add('text-white');
        
        // Reset message after 3 seconds
        setTimeout(() => {
            messageElement.textContent = 'Position the QR code within the frame';
            messageElement.classList.add('text-[#9CA3AF]');
            messageElement.classList.remove('text-white');
        }, 3000);
    }
    
    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanner
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop();
        }
        
        try {
            const paymentData = JSON.parse(decodedText);
            
            // Verify payment data
            if (!paymentData.reference || !paymentData.amount) {
                showError('Invalid QR code format');
                return;
            }
            
            // Send payment verification request
            fetch('/admin/verify-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message);
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                showError('Failed to verify payment');
                console.error('Error:', error);
            });
            
        } catch (error) {
            showError('Invalid QR code format');
            console.error('Error parsing QR code:', error);
        }
    }
    
    function onScanFailure(error) {
        // Handle scan failure silently
        console.warn(`QR Code scan failure: ${error}`);
    }
    
    function showSuccess(message) {
        const successMessage = document.getElementById('payment-success-message');
        const successDetails = document.getElementById('payment-success-details');
        const errorMessage = document.getElementById('payment-error-message');
        
        successDetails.textContent = message;
        successMessage.classList.remove('hidden');
        errorMessage.classList.add('hidden');
    }
    
    function showError(message) {
        const successMessage = document.getElementById('payment-success-message');
        const errorMessage = document.getElementById('payment-error-message');
        const errorDetails = document.getElementById('payment-error-details');
        
        errorDetails.textContent = message;
        errorMessage.classList.remove('hidden');
        successMessage.classList.add('hidden');
        
        // Restart scanner after 3 seconds
        setTimeout(() => {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop();
            }
            startScanner();
        }, 3000);
    }

    // Client-side search and filtering
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const filterType = document.getElementById('filterType');
        const dateFilter = document.getElementById('dateFilter');
        const rows = document.querySelectorAll('#invoiceTable tbody tr');
        
        const filterTable = () => {
            const searchTerm = searchInput.value.toLowerCase();
            const typeFilter = filterType.value.toLowerCase();
            const dateValue = dateFilter.value;
            
            rows.forEach(row => {
                const invoiceNumber = row.cells[0].textContent.toLowerCase();
                const clientName = row.cells[1].textContent.toLowerCase();
                const type = row.cells[2].textContent.toLowerCase();
                const date = row.cells[4].textContent;
                
                // Parse the date for comparison
                let shouldShow = true;
                
                // Check search term
                if (searchTerm && !invoiceNumber.includes(searchTerm) && !clientName.includes(searchTerm)) {
                    shouldShow = false;
                }
                
                // Check type filter
                if (typeFilter && !type.includes(typeFilter)) {
                    shouldShow = false;
                }
                
                // Check date filter (simplified)
                if (dateValue) {
                    const rowDate = new Date(date);
                    const filterDate = new Date(dateValue);
                    
                    if (rowDate.toDateString() !== filterDate.toDateString()) {
                        shouldShow = false;
                    }
                }
                
                row.style.display = shouldShow ? '' : 'none';
            });
        };
        
        searchInput.addEventListener('input', filterTable);
        filterType.addEventListener('change', filterTable);
        dateFilter.addEventListener('input', filterTable);
    });
</script>
@endsection

