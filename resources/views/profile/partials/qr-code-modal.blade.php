<!-- Modals -->
<div id="qrModal" class="modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-white">Your Check-In QR Code</h2>
            <button onclick="closeQrModal()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="bg-white p-4 rounded-lg flex justify-center items-center">
            <div class="w-64 h-64">
                {!! QrCode::size(250)->generate(Auth::user()->qr_code) !!}
            </div>
        </div>
        <p class="text-center mt-4 text-gray-300 text-sm">Show this QR code at the gym entrance to check in</p>
    </div>
</div> 