@extends('layouts.app')

@section('title', 'Payment Method')

@section('content')
<div class="min-h-screen bg-[#121212] text-white py-8 px-4 sm:px-6 lg:px-8 flex items-center justify-center bg-cover bg-center bg-no-repeat" style="background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ asset('assets/gym-bg.jpg') }}');">
    <div class="max-w-md w-full mx-auto bg-[#2d2d2d] rounded-xl shadow-lg overflow-hidden">
        <!-- Header Section -->
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Choose Payment Method</h2>
                <a href="{{ request()->query('type') === 'product' ? route('shop') : route('pricing.'.request()->query('type', 'gym')) }}" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </a>
            </div>
            <p class="text-sm text-gray-400">Select how you would like to pay for your {{ request()->query('type') === 'product' ? 'purchase' : 'subscription' }}.</p>
            
            <!-- Order Summary -->
            <div class="mt-4 p-4 bg-[#1e1e1e] rounded-lg">
                <h3 class="font-medium mb-2">Order Summary</h3>
                @if(request()->query('type') === 'product')
                    <div id="order-items" class="mt-2 pt-2 border-t border-gray-700">
                        <!-- Order items will be displayed here -->
                    </div>
                @else
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-400">Subscription:</span>
                        <span id="plan-display">{{ request()->query('plan') }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-400">Type:</span>
                        <span id="type-display">{{ request()->query('type') }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-medium mt-2 pt-2 border-t border-gray-700">
                    <span>Total:</span>
                    <span id="amount-display">₱{{ number_format(request()->query('amount', 0), 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Methods Section -->
        <div class="p-6 space-y-4">
            <div class="space-y-4">
                <!-- Cash Option -->
                <form id="cashForm" action="{{ route('payment.cash.qr') }}" method="POST">
                    @csrf
                    @if(request()->query('type') === 'product')
                        <input type="hidden" name="type" value="product">
                    @else
                        <input type="hidden" name="type" value="subscription">
                        <input type="hidden" name="subscription_type" value="{{ request()->query('type', 'gym') }}">
                    @endif
                    <input type="hidden" name="plan" value="{{ request()->query('plan') }}">
                    <input type="hidden" name="amount" value="{{ request()->query('amount') }}">
                    <input type="hidden" name="waiver_accepted" value="{{ request()->query('waiver_accepted', 0) }}">
                    <input type="hidden" name="payment_method" value="cash">
                    <input type="hidden" name="payment_status" value="pending">
                    <input type="hidden" name="order_data" id="order-data-cash">
                    
                    <button type="submit" class="w-full bg-[#1e1e1e] hover:bg-[#252525] transition-colors duration-200 p-4 rounded-lg flex items-center justify-between group">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded-full">
                                <i class="fas fa-money-bill text-green-400"></i>
                            </div>
                            <div>
                                <span class="font-medium block">Cash Payment</span>
                                <span class="text-sm text-gray-400">Pay at the counter</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-white transition-colors duration-200"></i>
                    </button>
                </form>
                
                <!-- PayMongo Option with programmatic form submission -->
                <div>
                    <button onclick="initiatePayMongoPayment()" class="w-full bg-[#1e1e1e] hover:bg-[#252525] transition-colors duration-200 p-4 rounded-lg flex items-center justify-between group">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 flex items-center justify-center bg-blue-700 rounded-full">
                                <i class="fas fa-credit-card text-white"></i>
                            </div>
                            <div>
                                <span class="font-medium block">Online Payment</span>
                                <span class="text-sm text-gray-400">Pay with PayMongo (Card, GCash, etc.)</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-white transition-colors duration-200"></i>
                    </button>
                </div>
                
                <!-- Hidden data for payment -->
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <input type="hidden" id="payment-type" value="{{ request()->query('type') === 'product' ? 'product' : 'subscription' }}">
                <input type="hidden" id="subscription-type" value="{{ request()->query('type', 'gym') }}">
                <input type="hidden" id="payment-plan" value="{{ request()->query('plan') }}">
                <input type="hidden" id="payment-amount" value="{{ request()->query('amount') }}">
                <input type="hidden" id="waiver-accepted" value="{{ request()->query('waiver_accepted', 0) }}">
                <input type="hidden" id="billing-name" value="{{ auth()->user()->name ?? 'Guest User' }}">
                <input type="hidden" id="billing-email" value="{{ auth()->user()->email ?? '' }}">
                <input type="hidden" id="billing-phone" value="{{ auth()->user()->mobile_number ?? '' }}">
                <input type="hidden" id="order-data-paymongo-hidden" value="">
            </div>
        </div>

        <!-- Information Section -->
        <div class="p-6 bg-[#1e1e1e] border-t border-gray-700">
            <div class="flex items-center space-x-2 text-sm text-gray-400">
                <i class="fas fa-lock"></i>
                <span>Payments are secure and encrypted</span>
            </div>
        </div>
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
                    <div id="scanner" class="w-full h-64 bg-black rounded-lg"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-48 h-48 border-2 border-white rounded-lg"></div>
                    </div>
                </div>
                
                <!-- Scanner controls -->
                <div class="flex justify-center space-x-3 mb-4">
                    <button id="switchCamera" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i> Switch Camera
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
document.addEventListener('DOMContentLoaded', function() {
    console.log('Payment page loaded');
    
    // Function to initialize PayMongo payment
    window.initiatePayMongoPayment = function() {
        console.log('Initiating PayMongo payment');
        
        // Show loading state on button
        const payBtn = document.querySelector('button[onclick="initiatePayMongoPayment()"]');
        if (payBtn) {
            payBtn.disabled = true;
            payBtn.innerHTML = '<div class="flex items-center space-x-2"><div class="w-5 h-5 border-t-2 border-b-2 border-white rounded-full animate-spin"></div><span>Processing...</span></div>';
        }
        
        // Get form data from hidden fields
        const paymentType = document.getElementById('payment-type').value;
        const subscriptionType = document.getElementById('subscription-type').value;
        const paymentPlan = document.getElementById('payment-plan').value;
        const paymentAmount = document.getElementById('payment-amount').value;
        const waiverAccepted = document.getElementById('waiver-accepted').value;
        const billingName = document.getElementById('billing-name').value;
        const billingEmail = document.getElementById('billing-email').value;
        const billingPhone = document.getElementById('billing-phone').value;
        
        // Get order data if available
        const orderDataStr = sessionStorage.getItem('orderData');
        const orderDataHiddenField = document.getElementById('order-data-paymongo-hidden');
        if (orderDataStr && orderDataHiddenField) {
            orderDataHiddenField.value = orderDataStr;
        }
        
        // Create a hidden form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("payment.process") }}';
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfToken);
        
        // Add all necessary fields
        const fieldsToAdd = [
            { name: 'type', value: paymentType },
            { name: 'subscription_type', value: subscriptionType },
            { name: 'plan', value: paymentPlan },
            { name: 'amount', value: paymentAmount },
            { name: 'waiver_accepted', value: waiverAccepted },
            { name: 'payment_method', value: 'paymongo' },
            { name: 'billing_name', value: billingName },
            { name: 'billing_email', value: billingEmail },
            { name: 'billing_phone', value: billingPhone }
        ];
        
        // Add order data if available
        if (orderDataStr) {
            fieldsToAdd.push({ name: 'order_data', value: orderDataStr });
        }
        
        // Append all fields to the form
        fieldsToAdd.forEach(field => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = field.name;
            input.value = field.value;
            form.appendChild(input);
        });
        
        // Append form to document body and submit
        document.body.appendChild(form);
        console.log('Submitting form...');
        form.submit();
    };
    
    // Get order data from sessionStorage
    const orderDataStr = sessionStorage.getItem('orderData');
    if (orderDataStr) {
        const orderData = JSON.parse(orderDataStr);
        
        // Set hidden form fields with order data
        document.getElementById('order-data-cash').value = orderDataStr;
        document.getElementById('order-data-paymongo').value = orderDataStr;
        
        // Show order items in summary
        const orderItemsContainer = document.getElementById('order-items');
        if (orderItemsContainer && orderData.items && orderData.items.length > 0) {
            // Clear existing content
            orderItemsContainer.innerHTML = '';
            
            // Add items heading
            const heading = document.createElement('div');
            heading.className = 'text-sm text-gray-400 mb-2';
            heading.textContent = 'Items:';
            orderItemsContainer.appendChild(heading);
            
            // Add each item
            orderData.items.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'flex justify-between text-sm mb-1 pl-2';
                
                const nameElement = document.createElement('span');
                nameElement.className = 'text-gray-300';
                nameElement.textContent = `${item.name} x${item.quantity}`;
                
                const priceElement = document.createElement('span');
                priceElement.textContent = `₱${(parseFloat(item.price) * item.quantity).toFixed(2)}`;
                
                itemElement.appendChild(nameElement);
                itemElement.appendChild(priceElement);
                orderItemsContainer.appendChild(itemElement);
            });
        }
    }
    
    // QR Scanner code
    let html5QrScanner = null;
    let currentCameraId = null;
    let availableCameras = [];
    
    // Initially hide the switch camera button until we know there are multiple cameras
    document.getElementById('switchCamera').style.display = 'none';
    
    // Add event listener to switch camera button
    document.getElementById('switchCamera').addEventListener('click', function() {
        switchCamera();
    });
    
    // Function to open scanner
    window.openScanner = function() {
        document.getElementById('scannerModal').classList.remove('hidden');
        // Start scanner automatically
        startScanner();
    };
    
    // Function to close scanner
    window.closeScanner = function() {
        document.getElementById('scannerModal').classList.add('hidden');
        if (html5QrScanner) {
            html5QrScanner.stop()
                .catch(error => console.error("Error stopping scanner:", error));
        }
    };
    
    // Function to start scanner
    async function startScanner() {
        try {
            // Get available cameras
            const devices = await Html5Qrcode.getCameras();
            availableCameras = devices;
            
            if (availableCameras.length === 0) {
                showError('No cameras found');
                return;
            }
            
            // Use the first camera by default or continue with current if switching
            if (!currentCameraId) {
                currentCameraId = availableCameras[0].id;
            }
            
            // Show switch camera button only if multiple cameras are available
            document.getElementById('switchCamera').style.display = 
                availableCameras.length > 1 ? 'flex' : 'none';
            
            // Stop previous scanner instance if exists
            if (html5QrScanner) {
                await html5QrScanner.stop();
            }
            
            // Create new scanner
            html5QrScanner = new Html5Qrcode("scanner");
            
            const qrConfig = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };
            
            await html5QrScanner.start(
                { deviceId: { exact: currentCameraId } },
                qrConfig,
                onScanSuccess,
                onScanFailure
            );
            
            showMessage('Scanner started. Position QR code within frame.');
            
        } catch (error) {
            showError('Failed to start scanner: ' + error.message);
            console.error('Error starting scanner:', error);
        }
    }
    
    // Function to switch camera
    function switchCamera() {
        if (availableCameras.length <= 1) {
            showError('No additional cameras available');
            return;
        }
        
        // Find current camera index
        const currentIndex = availableCameras.findIndex(camera => camera.id === currentCameraId);
        // Switch to next camera
        const nextIndex = (currentIndex + 1) % availableCameras.length;
        currentCameraId = availableCameras[nextIndex].id;
        
        // Show which camera is being used
        showMessage(`Switched to camera ${nextIndex + 1}/${availableCameras.length}`);
        
        // Restart scanner with new camera
        startScanner();
    }
    
    // Function to show message
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
    
    // Function on successful scan
    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanner
        if (html5QrScanner) {
            html5QrScanner.stop().catch(err => console.error(err));
        }
        
        try {
            // Get order data from sessionStorage
            const orderDataStr = sessionStorage.getItem('orderData');
            if (!orderDataStr) {
                showError('Order data not found');
                return;
            }
            
            const orderData = JSON.parse(orderDataStr);
            
            // Try to parse the QR code as JSON if it's in JSON format
            let qrData;
            try {
                qrData = JSON.parse(decodedText);
            } catch (e) {
                // If not JSON, use the raw text
                qrData = { code: decodedText };
            }
            
            // Create payment data to send to server
            const paymentData = {
                qr_code: decodedText,
                reference: qrData.reference || qrData.code || decodedText,
                amount: orderData.amount,
                order_data: orderData,
                payment_method: 'cash'
            };
            
            showMessage('Processing payment...');
            
            // Send payment verification request
            fetch('/payment/process-cash', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showSuccess(data.message || 'Payment successful!');
                    
                    // Clear order data from sessionStorage
                    sessionStorage.removeItem('orderData');
                    
                    // Redirect to orders page after a short delay
                    setTimeout(() => {
                        window.location.href = '{{ route("orders") }}?success=true';
                    }, 2000);
                } else {
                    showError(data.message || 'Payment verification failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Failed to process payment. Please try again.');
            });
            
        } catch (error) {
            console.error('Error processing QR code:', error);
            showError('Invalid QR code or order data format');
        }
    }
    
    // Function on scan failure
    function onScanFailure(error) {
        // Handle scan failure silently - this is normal when no QR code is in view
        console.warn(`QR Code scan failure: ${error}`);
    }
    
    // Function to show success message
    function showSuccess(message) {
        const successMessage = document.getElementById('payment-success-message');
        const successDetails = document.getElementById('payment-success-details');
        const errorMessage = document.getElementById('payment-error-message');
        
        successDetails.textContent = message;
        successMessage.classList.remove('hidden');
        errorMessage.classList.add('hidden');
    }
    
    // Function to show error message
    function showError(message) {
        const successMessage = document.getElementById('payment-success-message');
        const errorMessage = document.getElementById('payment-error-message');
        const errorDetails = document.getElementById('payment-error-details');
        
        errorDetails.textContent = message;
        errorMessage.classList.remove('hidden');
        successMessage.classList.add('hidden');
        
        // Restart scanner after 3 seconds
        setTimeout(() => {
            startScanner();
        }, 3000);
    }
});
</script>
@endsection