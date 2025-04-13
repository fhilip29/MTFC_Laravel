<div id="waiverModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-10 max-w-4xl w-full max-h-[90vh] overflow-y-auto relative">
        <!-- Modal Header -->
        <div class="text-center mb-6">
            <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="MTFC Logo" class="h-16 mx-auto mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Waiver and Release Liability</h2>
        </div>

        <!-- Waiver Content -->
        <div class="text-gray-700 space-y-4 mb-6">
            <p>I agree that by participating in physical exercise of training activities and/or by using any experience equipment or by <span class="font-semibold">SPARRING</span> in any form of <span class="font-semibold">COMBAT SPORTS</span>, I do it entirely at my own risk and in good physical and health condition and I assume possibility of injury, illness or death.</p>

            <p>Manila Total Fitness Center is also not responsible for the loss of any/all of my personal property.</p>

            <p>Any changes in diet including the use of food supplements, weight reduction and/or body building enhancement products are entirely my responsibility.</p>

            <p>I acknowledge that I have carefully read this waiver and expressly agree to release and discharge the trainer/instructor, Mr. HAMAD TIALUMPA and/or Manila Total Fitness Center from any and all other claims or causes of action that may arise with respect to my use and/or participation at Manila Total Fitness Center.</p>

            <p>Further, I understand that Manila Total Fitness Center services are non-refundable, non-transferable and have noted the expiration period for every service.</p>
        </div>

        <!-- Checkbox Agreement -->
        <div class="flex items-start gap-2 mb-6">
            <input type="checkbox" id="waiverAgreement" class="mt-1">
            <label for="waiverAgreement" class="text-sm text-gray-700">I have read and agree to the waiver terms and <a href="{{ route('terms') }}" target="_blank" class="text-blue-600 hover:underline">Terms of Use</a></label>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4">
            <button onclick="closeWaiverModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition">
                Cancel
            </button>
            <button onclick="acceptWaiver()" id="continueBtn" disabled class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                Continue
            </button>
        </div>
    </div>
</div>

<script>
function openWaiverModal() {
    document.getElementById('waiverModal').classList.remove('hidden');
}

function closeWaiverModal() {
    document.getElementById('waiverModal').classList.add('hidden');
}

document.getElementById('waiverAgreement').addEventListener('change', function() {
    document.getElementById('continueBtn').disabled = !this.checked;
});

function acceptWaiver() {
    if (document.getElementById('waiverAgreement').checked) {
        // Here you can add the logic to proceed with subscription
        closeWaiverModal();
        // Redirect to payment or subscription page
        window.location.href = '/subscribe'; // Update this URL as needed
    }
}
</script>