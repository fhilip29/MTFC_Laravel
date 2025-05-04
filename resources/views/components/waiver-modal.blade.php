<!-- Waiver Modal -->
<div id="waiverModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black opacity-50 transition-opacity" id="waiverModalBg"></div>
        
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-xl">
            <div class="p-6">
                <div class="flex flex-col items-center mb-4">
                    <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="MTFC Logo" class="h-16 mb-4">
                    <h3 class="text-xl font-bold text-center text-gray-900">Waiver and Release Liability</h3>
                </div>
                
                <div class="mt-4 mb-6 text-center px-4">
                    <div class="text-sm text-gray-700 space-y-4">
                        <p>I agree that by participating in physical exercise of training activities and/or by using any experience equipment or by <span class="font-semibold">SPARRING</span> in any form of <span class="font-semibold">COMBAT SPORTS</span>. I do it entirely at my own risk and in good physical and health condition and I assume possibility of injury, illness or death.</p>
                        
                        <p>Manila Total Fitness Center is also not responsible for the loss of any/all of my personal property.</p>
                        
                        <p>Any changes in diet including the use of food supplements, weight reduction and/or body building enhancement products are entirely my responsibility.</p>
                        
                        <p>I acknowledge that I have carefully read this waiver and expressly agree to release and discharge the trainer/instructor, Mr. HAMAD TIALUMPA and/or Manila Total Fitness Center from any and all other claims or causes of action that may arise with respect to my use and/or participation at Manila Total fitness Center.</p>
                        
                        <p>Further, I understand that Manila Total Fitness Center services are non-refundable non-transferable and have noted the expiration period for each every service.</p>
                    </div>
                    
                    <div class="flex items-center justify-center gap-2 mt-6 mb-6">
                        <input id="waiverCheck" type="checkbox" class="w-4 h-4 border rounded focus:ring-2">
                        <label for="waiverCheck" class="text-sm font-medium text-gray-700">
                            I have read and agree to the waiver terms
                        </label>
                    </div>
                    
                    <form id="subscriptionForm" action="{{ route('subscription.store') }}" method="POST" class="hidden">
                        @csrf
                        <input type="hidden" id="subType" name="type" value="">
                        <input type="hidden" id="subPlan" name="plan" value="">
                        <input type="hidden" id="subPrice" name="amount" value="">
                        <input type="hidden" id="waiverAccepted" name="waiver_accepted" value="1">
                    </form>
                    
                    <div class="flex justify-center space-x-3">
                        <button type="button" id="waiverCancelBtn" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition">
                            Cancel
                        </button>
                        <button type="button" id="waiverAgreeBtn" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Continue
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const waiverModal = document.getElementById('waiverModal');
        const closeWaiverModal = document.getElementById('closeWaiverModal');
        const waiverModalBg = document.getElementById('waiverModalBg');
        const waiverCancelBtn = document.getElementById('waiverCancelBtn');
        const waiverCheck = document.getElementById('waiverCheck');
        const waiverAgreeBtn = document.getElementById('waiverAgreeBtn');
        const subscriptionForm = document.getElementById('subscriptionForm');
        
        // Function to open modal
        window.openWaiverModal = function(type, plan, price) {
            waiverModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            
            // Set subscription details in hidden form
            if (type && plan && price) {
                document.getElementById('subType').value = type;
                document.getElementById('subPlan').value = plan;
                document.getElementById('subPrice').value = price;
                console.log('Subscription details set:', {type, plan, price});
            }
        };
        
        // Function to close modal
        function closeModal() {
            waiverModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            waiverCheck.checked = false;
            waiverAgreeBtn.disabled = true;
        }
        
        // Close modal with background click
        waiverModalBg.addEventListener('click', closeModal);
        
        // Close modal with Cancel button
        waiverCancelBtn.addEventListener('click', closeModal);
        
        // Toggle agree button state
        waiverCheck.addEventListener('change', function() {
            waiverAgreeBtn.disabled = !this.checked;
        });
        
        // Redirect to payment method page when agree button is clicked
        waiverAgreeBtn.addEventListener('click', function() {
            if (waiverCheck.checked) {
                // Get subscription details
                const type = document.getElementById('subType').value;
                const plan = document.getElementById('subPlan').value;
                const price = document.getElementById('subPrice').value;
                
                // Close the modal
                closeModal();
                
                // Show loading indicator
                Swal.fire({
                    title: 'Redirecting to Payment',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    timer: 1500,
                    timerProgressBar: true
                }).then(() => {
                    // Redirect to payment method page with subscription details as query parameters
                    window.location.href = `/payment-method?type=${encodeURIComponent(type)}&plan=${encodeURIComponent(plan)}&amount=${encodeURIComponent(price)}&waiver_accepted=1`;
                });
            }
        });
    });
</script>