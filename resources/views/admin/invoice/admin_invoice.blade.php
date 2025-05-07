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
                <button id="openScannerBtn" class="bg-[#374151] hover:bg-[#4B5563] text-white font-semibold flex items-center gap-2 px-4 py-2 rounded-lg shadow transition-colors w-full sm:w-auto justify-center">
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
<div id="scannerModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div id="scannerModalOverlay" class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-black opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-[#1F2937] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-[#374151]">
            <div class="bg-[#1F2937] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-white flex items-center justify-between relative z-10">
                            <span>Payment QR Scanner</span>
                            <button id="closeScanner" class="text-[#9CA3AF] hover:text-white transition-colors">
                                <i class="fas fa-times"></i>
                </button>
                        </h3>
            
                <!-- Success message (initially hidden) -->
                        <div id="payment-success-message" class="hidden bg-green-600 text-white p-4 rounded-lg mt-4 mb-4 animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-2xl mr-2"></i>
                        <div>
                            <h4 class="font-bold">Payment Confirmed!</h4>
                            <p class="text-sm" id="payment-success-details"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Error message (initially hidden) -->
                        <div id="payment-error-message" class="hidden bg-red-600 text-white p-4 rounded-lg mt-4 mb-4 animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-2xl mr-2"></i>
                        <div>
                            <h4 class="font-bold">Payment Error</h4>
                            <p class="text-sm" id="payment-error-details"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Scanner container -->
                        <div class="relative mt-6 mb-6">
                            <div id="reader" class="w-full h-[400px] rounded-lg mx-auto"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-[#111827] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse relative z-10">
                <button id="cancelScanner" class="mt-3 w-full inline-flex justify-center rounded-md border border-[#374151] shadow-sm px-4 py-2 bg-[#1F2937] text-base font-medium text-[#9CA3AF] hover:bg-[#374151] focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                    </button>
            </div>
        </div>
    </div>
</div>

<!-- Import Html5QrcodeScanner library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<!-- Import SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Initialize elements
    const openScannerBtn = document.getElementById('openScannerBtn');
    const scannerModal = document.getElementById('scannerModal');
    const closeScanner = document.getElementById('closeScanner');
    const cancelScanner = document.getElementById('cancelScanner');
    const scannerModalOverlay = document.getElementById('scannerModalOverlay');
    const successMessage = document.getElementById('payment-success-message');
    const successDetails = document.getElementById('payment-success-details');
    const errorMessage = document.getElementById('payment-error-message');
    const errorDetails = document.getElementById('payment-error-details');
    
    let html5QrCode = null;
    let isScanning = false;
    
    // Open scanner modal
    openScannerBtn.addEventListener('click', function() {
        scannerModal.classList.remove('hidden');
                startScanner();
    });
    
    // Close scanner modal
    function closeModal() {
        scannerModal.classList.add('hidden');
        stopScanner();
    }
    
    closeScanner.addEventListener('click', closeModal);
    cancelScanner.addEventListener('click', closeModal);
    
    // Close modal when clicking on overlay
    scannerModalOverlay.addEventListener('click', function(e) {
        if (e.target === scannerModalOverlay) {
            closeModal();
        }
    });

    // Start the scanner
    function startScanner() {
        if (isScanning) return;
        
        // Clear any previous messages
        successMessage.classList.add('hidden');
        errorMessage.classList.add('hidden');
        
        // Calculate optimal QR box size
        const readerElement = document.getElementById('reader');
        const readerWidth = readerElement.clientWidth;
        const readerHeight = readerElement.clientHeight;
        const qrboxSize = Math.min(readerWidth, readerHeight) * 0.7; // 70% of the smaller dimension
        
        const config = {
            fps: 10,
            qrbox: { width: qrboxSize, height: qrboxSize },
            rememberLastUsedCamera: true,
            aspectRatio: 1.0,
            videoConstraints: {
                facingMode: "environment",
                width: { min: 640, ideal: 1280, max: 1920 },
                height: { min: 480, ideal: 720, max: 1080 }
            }
        };
        
        html5QrCode = new Html5Qrcode("reader");
        
        // This method will trigger camera selection and QR scanning
        html5QrCode.start(
            { facingMode: "environment" }, // Prefer back camera
            config,
                onScanSuccess,
                onScanFailure
        )
        .then(() => {
            isScanning = true;
            console.log("QR Code scanning started");
            
            // Apply some styling to the scanner
            const scannerElement = document.getElementById('reader');
            if (scannerElement) {
                // Override some of the default styling
                scannerElement.style.border = "none";
            
                // Target internal elements with more specific selectors
                setTimeout(() => {
                    // Get all video elements inside the reader and style them
                    const videoElement = scannerElement.querySelector('video');
                    if (videoElement) {
                        videoElement.style.width = '100%';
                        videoElement.style.height = '100%';
                        videoElement.style.objectFit = 'cover';
                        videoElement.style.borderRadius = '0.5rem';
                    }
                    
                    // Style the scanning region if it exists
                    const scanRegion = document.getElementById('reader__scan_region');
                    if (scanRegion) {
                        scanRegion.style.display = 'flex';
                        scanRegion.style.justifyContent = 'center';
                        scanRegion.style.alignItems = 'center';
        }
        
                    // Adjust canvas position if it exists
                    const canvas = scannerElement.querySelector('canvas');
                    if (canvas) {
                        canvas.style.position = 'absolute';
                        canvas.style.left = '50%';
                        canvas.style.top = '50%';
                        canvas.style.transform = 'translate(-50%, -50%)';
            }
                }, 500);
            }
        })
        .catch((err) => {
            console.error("Error starting QR Code scanner:", err);
            showError("Could not start camera: " + err.message);
            
            // Try with a more generic config as fallback
            html5QrCode.start(
                { facingMode: { exact: "environment" } },
                { fps: 10, qrbox: qrboxSize },
                onScanSuccess,
                onScanFailure
            )
            .then(() => {
                isScanning = true;
                console.log("QR Code scanning started with fallback config");
            })
            .catch((err) => {
                console.error("Error starting QR Code scanner with fallback:", err);
                showError("Failed to start camera. Please ensure camera permissions are granted and try again.");
            });
        });
    }
    
    // Stop the scanner
    function stopScanner() {
        if (html5QrCode && isScanning) {
            html5QrCode.stop()
                .then(() => {
                    isScanning = false;
                    console.log("QR Code scanning stopped");
                })
                .catch((err) => {
                    console.error("Error stopping QR Code scanner:", err);
                });
        }
    }
    
    // QR Code scan success callback
    function onScanSuccess(decodedText, decodedResult) {
        console.log("QR Code detected:", decodedText);
        
        // Stop scanning once a QR code is found
        stopScanner();
        
        try {
            // Try to parse the QR code as JSON
            let paymentData;
            try {
                paymentData = JSON.parse(decodedText);
                console.log("Parsed QR code data:", paymentData);
            } catch (e) {
                // If not JSON, just use the string
                paymentData = { reference: decodedText };
                console.log("Using QR code as reference string:", paymentData);
            }
            
            // Show processing message
            Swal.fire({
                title: 'Processing...',
                text: 'Verifying payment information',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Add missing fields if they don't exist in the parsed data
            if (!paymentData.type && paymentData.plan) {
                paymentData.type = 'subscription';
            } else if (!paymentData.type && paymentData.items) {
                paymentData.type = 'product';
            }
            
            if (!paymentData.order_id && paymentData.reference) {
                // Use reference as order_id if not provided
                paymentData.order_id = paymentData.reference;
            }
            
            // Send to server for processing
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
                Swal.close();
                if (data.success) {
                    showSuccess(data.message || "Payment verified successfully!");
                    
                    // Reload page after a delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showError(data.message || "Error verifying payment");
                    
                    // Try to restart scanner after error
                    setTimeout(() => {
                        startScanner();
                    }, 3000);
                }
            })
            .catch(error => {
                Swal.close();
                console.error("Error:", error);
                showError("Network error. Please try again.");
                
                // Try to restart scanner after error
                setTimeout(() => {
                    startScanner();
                }, 3000);
            });
        } catch (error) {
            console.error("Error processing QR code:", error);
            showError("Invalid QR code format");
            
            // Try to restart scanner after error
            setTimeout(() => {
                startScanner();
            }, 3000);
        }
    }
    
    // QR Code scan failure callback
    function onScanFailure(error) {
        // Silent failure, no need to show errors for each frame
        console.debug(`QR scan error: ${error}`);
    }
    
    // Show success message
    function showSuccess(message) {
        successDetails.textContent = message;
        successMessage.classList.remove('hidden');
        errorMessage.classList.add('hidden');
    }
    
    // Show error message
    function showError(message) {
        errorDetails.textContent = message;
        errorMessage.classList.remove('hidden');
        successMessage.classList.add('hidden');
    }

    // Client-side search and filtering
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
        
    // Add event listeners for filtering
    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (filterType) filterType.addEventListener('change', filterTable);
    if (dateFilter) dateFilter.addEventListener('input', filterTable);
    });
</script>

<style>
/* QR Scanner styling */
#reader {
    border: none !important;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    border-radius: 0.5rem;
    background-color: black;
    overflow: hidden;
    max-width: 100%;
    margin: 0 auto;
}

#reader video {
    object-fit: cover !important;
    border-radius: 0.5rem;
    width: 100% !important;
    height: 100% !important;
}

#reader__dashboard {
    padding: 0 !important;
}

#reader__scan_region {
    background: transparent !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    min-height: 300px !important;
}

#reader__scan_region img {
    display: none !important;
}

/* Center the scan region */
#reader canvas {
    left: 50% !important;
    top: 50% !important;
    transform: translate(-50%, -50%) !important;
}

/* Success/Error message animation */
@keyframes pulse {
    0% { opacity: 0.8; }
    50% { opacity: 1; }
    100% { opacity: 0.8; }
}

.animate-pulse {
    animation: pulse 2s infinite;
}
</style>
@endsection

