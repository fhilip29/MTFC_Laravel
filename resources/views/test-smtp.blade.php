<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMTP Test Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h1 class="text-2xl font-bold text-center mb-6">SMTP Test Tool</h1>
        
        <div id="result-box" class="mb-6 p-4 rounded-lg border hidden">
            <h3 class="font-bold mb-2">Test Results:</h3>
            <pre id="result" class="whitespace-pre-wrap text-sm bg-gray-50 p-3 rounded overflow-auto max-h-60"></pre>
        </div>

        <form id="test-form" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Enter recipient email"
                    class="w-full p-3 border border-gray-300 rounded"
                    required
                >
            </div>

            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <input 
                    type="text" 
                    id="subject" 
                    name="subject" 
                    value="SMTP Test Email"
                    class="w-full p-3 border border-gray-300 rounded"
                    required
                >
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea 
                    id="message" 
                    name="message" 
                    rows="3" 
                    class="w-full p-3 border border-gray-300 rounded"
                    required
                >This is a test email to verify SMTP settings.</textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded font-medium">
                Send Test Email
            </button>
        </form>
        
        <div class="mt-6 text-sm text-gray-600">
            <p>This tool tests the SMTP settings configured in your .env file.</p>
            <p class="mt-2">Current Mail Settings:</p>
            <table class="w-full text-xs mt-1">
                <tr>
                    <td class="font-semibold pr-2">Driver:</td>
                    <td>{{ config('mail.default') }}</td>
                </tr>
                <tr>
                    <td class="font-semibold pr-2">Host:</td>
                    <td>{{ config('mail.mailers.smtp.host') }}</td>
                </tr>
                <tr>
                    <td class="font-semibold pr-2">Port:</td>
                    <td>{{ config('mail.mailers.smtp.port') }}</td>
                </tr>
                <tr>
                    <td class="font-semibold pr-2">From Address:</td>
                    <td>{{ config('mail.from.address') }}</td>
                </tr>
                <tr>
                    <td class="font-semibold pr-2">From Name:</td>
                    <td>{{ config('mail.from.name') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('test-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            
            // Show loading
            Swal.fire({
                title: 'Sending...',
                text: 'Attempting to send test email',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Show the result box and prepare it for new results
            const resultBox = document.getElementById('result-box');
            const resultPre = document.getElementById('result');
            resultBox.classList.remove('hidden', 'bg-green-50', 'border-green-200', 'bg-red-50', 'border-red-200');
            resultBox.classList.add('bg-gray-50', 'border-gray-200');
            resultPre.textContent = "Sending request...";
            
            try {
                console.log('Sending request to /test-smtp-send');
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
                
                const response = await fetch('/test-smtp-send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email, subject, message }),
                    signal: controller.signal
                });
                
                clearTimeout(timeoutId);
                console.log('Response status:', response.status);
                
                // First check if the response is ok before trying to parse JSON
                if (!response.ok) {
                    const responseText = await response.text();
                    console.error('Server error:', response.status, responseText);
                    throw new Error(`Server responded with ${response.status}: ${responseText}`);
                }
                
                // Try to parse the JSON
                let data;
                try {
                    data = await response.json();
                    console.log('Response data:', data);
                } catch (jsonError) {
                    console.error('JSON parsing error:', jsonError);
                    const responseText = await response.text();
                    console.log('Raw response:', responseText);
                    throw new Error(`Invalid JSON response: ${jsonError.message}`);
                }
                
                // Update the result box
                resultBox.classList.remove('bg-gray-50', 'border-gray-200');
                resultBox.classList.add(data.success ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200');
                resultPre.textContent = JSON.stringify(data, null, 2);
                
                // Show alert
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? 'Email Sent' : 'Failed to Send',
                    text: data.message,
                    confirmButtonText: 'OK'
                });
                
            } catch (error) {
                console.error('Request error:', error);
                
                // Update the result box with error details
                resultBox.classList.remove('bg-gray-50', 'border-gray-200');
                resultBox.classList.add('bg-red-50', 'border-red-200');
                resultPre.textContent = `Error: ${error.message}\n\nCheck browser console for more details.`;
                
                let errorTitle = 'Connection Error';
                let errorMessage = 'Could not connect to the server. Please check your internet connection.';
                
                if (error.name === 'AbortError') {
                    errorTitle = 'Request Timeout';
                    errorMessage = 'The server took too long to respond. This might indicate a server-side issue.';
                } else if (error.message.includes('Server responded with')) {
                    errorTitle = 'Server Error';
                    errorMessage = error.message;
                } else if (error.message.includes('Invalid JSON')) {
                    errorTitle = 'Response Error';
                    errorMessage = 'The server response was not in the expected format.';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: errorTitle,
                    text: errorMessage,
                    footer: 'Check the browser console (F12) for technical details',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
</body>
</html> 