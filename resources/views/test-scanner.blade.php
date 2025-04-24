<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>QR Scanner Test (HTML5-QRCode)</title>
    <!-- Use html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .result-container {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f3f4f6;
            border-radius: 0.5rem;
        }
        .camera-selection {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6 text-center">QR Scanner Test (HTML5-QRCode)</h1>
            
            <div class="flex flex-col gap-4">
                <div class="text-center mb-4">
                    <p id="status-message" class="mb-4 text-gray-700">Choose a camera and start scanning</p>
                </div>
                
                <div class="camera-selection mx-auto max-w-md">
                    <select id="camera-select" class="block w-full p-2 border border-gray-300 rounded-md mb-2">
                        <option value="">Loading cameras...</option>
                    </select>
                    
                    <div class="flex space-x-2 justify-center">
                        <button id="start-button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                            Start Scanner
                        </button>
                        <button id="stop-button" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md" disabled>
                            Stop Scanner
                        </button>
                    </div>
                </div>
                
                <!-- QR Code Reader Element -->
                <div id="reader" class="mx-auto"></div>
                
                <div class="result-container">
                    <h2 class="font-bold mb-2">Scan Results:</h2>
                    <pre id="result" class="text-sm whitespace-pre-wrap">No QR code scanned yet</pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startButton = document.getElementById('start-button');
            const stopButton = document.getElementById('stop-button');
            const statusMessage = document.getElementById('status-message');
            const cameraSelect = document.getElementById('camera-select');
            const resultElement = document.getElementById('result');
            
            // Global variables
            let html5QrCode = null;
            let selectedDeviceId = null;
            let isScanning = false;
            
            // Setup camera selection
            function populateCameraOptions() {
                statusMessage.textContent = 'Looking for cameras...';
                
                Html5Qrcode.getCameras()
                    .then(devices => {
                        if (devices && devices.length) {
                            cameraSelect.innerHTML = '';
                            devices.forEach(device => {
                                const option = document.createElement('option');
                                option.value = device.id;
                                option.text = device.label || `Camera ${cameraSelect.length + 1}`;
                                cameraSelect.appendChild(option);
                            });
                            
                            selectedDeviceId = devices[0].id;
                            statusMessage.textContent = `${devices.length} camera(s) found. Select one and click Start.`;
                        } else {
                            cameraSelect.innerHTML = '<option value="">No cameras found</option>';
                            statusMessage.textContent = 'No cameras detected on this device';
                        }
                    })
                    .catch(err => {
                        console.error('Error getting cameras', err);
                        statusMessage.textContent = `Error: ${err.message || 'Could not access cameras'}`;
                    });
            }
            
            // Initialize the scanner
            function initScanner() {
                html5QrCode = new Html5Qrcode("reader");
                populateCameraOptions();
            }
            
            // Start scanning
            function startScanner() {
                if (!html5QrCode) {
                    initScanner();
                    return;
                }
                
                const deviceId = cameraSelect.value;
                if (!deviceId) {
                    statusMessage.textContent = 'Please select a camera first';
                    return;
                }
                
                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
                };
                
                html5QrCode.start(
                    deviceId, 
                    config,
                    onScanSuccess,
                    onScanFailure
                )
                .then(() => {
                    isScanning = true;
                    startButton.disabled = true;
                    stopButton.disabled = false;
                    statusMessage.textContent = 'Scanning active. Point camera at a QR code.';
                    statusMessage.className = 'mb-4 text-green-600';
                })
                .catch(err => {
                    console.error('Error starting camera:', err);
                    statusMessage.textContent = `Error starting camera: ${err.message || 'Unknown error'}`;
                    statusMessage.className = 'mb-4 text-red-600';
                });
            }
            
            // Stop scanning
            function stopScanner() {
                if (html5QrCode && isScanning) {
                    html5QrCode.stop()
                        .then(() => {
                            isScanning = false;
                            startButton.disabled = false;
                            stopButton.disabled = true;
                            statusMessage.textContent = 'Scanner stopped';
                            statusMessage.className = 'mb-4 text-gray-700';
                        })
                        .catch(err => {
                            console.error('Error stopping scanner:', err);
                        });
                }
            }
            
            // QR Code scan success callback
            function onScanSuccess(decodedText, decodedResult) {
                console.log(`QR Code detected: ${decodedText}`, decodedResult);
                
                const result = {
                    data: decodedText,
                    details: decodedResult,
                    timestamp: new Date().toISOString()
                };
                
                resultElement.textContent = JSON.stringify(result, null, 2);
                
                // Visual feedback (optional, can be removed if we want continuous scanning)
                statusMessage.textContent = 'QR Code found!';
                statusMessage.className = 'mb-4 text-green-600 font-bold';
                
                // If you want to stop after first successful scan, uncomment the next line
                // stopScanner();
            }
            
            // QR Code scan error callback (we can ignore most errors as they just mean no QR code in frame)
            function onScanFailure(error) {
                // Don't log or show scanning errors as they happen frequently when no QR is in view
                // console.error(`QR Scan error: ${error}`);
            }
            
            // Event listeners
            startButton.addEventListener('click', startScanner);
            stopButton.addEventListener('click', stopScanner);
            cameraSelect.addEventListener('change', function() {
                selectedDeviceId = this.value;
                if (isScanning) {
                    stopScanner();
                    setTimeout(() => startScanner(), 300);
                }
            });
            
            // Initialize on page load
            initScanner();
        });
    </script>
</body>
</html> 