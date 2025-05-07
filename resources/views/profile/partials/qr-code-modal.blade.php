<!-- QR Code Modal -->
<div id="qrModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Check-In QR Code</h3>
            <button onclick="closeQrModal()" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="flex justify-center">
            <div class="bg-white p-3 rounded-lg border border-gray-200">
                <div class="w-60 h-60">
                    {!! QrCode::size(240)->generate(Auth::user()->qr_code) !!}
                </div>
            </div>
        </div>
        
        <p class="text-center text-gray-600 text-sm mt-4">
            Show this QR code to the staff for check-in and check-out.
        </p>
    </div>
</div> 