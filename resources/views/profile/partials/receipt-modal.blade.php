<!-- Receipt Modal -->
<div id="receiptModal" class="modal">
    <div class="modal-content max-w-md bg-white border border-gray-200 rounded-lg shadow-lg">
        <div class="flex justify-between items-center mb-4 p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-receipt text-gray-500"></i> 
                <span id="receipt-title">Receipt</span>
            </h2>
            <div class="flex items-center gap-2">
                <button onclick="printReceipt()" title="Print Receipt" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-print"></i>
                </button>
                <button onclick="closeReceiptModal()" title="Close" class="text-gray-500 hover:text-gray-700 transition-colors text-lg">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <div id="receipt-content" class="p-4 max-h-[60vh] overflow-y-auto">
            <!-- Receipt Details -->
            <div class="grid grid-cols-3 gap-4 mb-4 text-xs">
                <div>
                    <div class="text-gray-500 uppercase tracking-wider">Receipt No.</div>
                    <div class="text-gray-800 font-mono mt-1" id="receiptInvoiceNumber"></div>
                </div>
                <div>
                    <div class="text-gray-500 uppercase tracking-wider">Date</div>
                    <div class="text-gray-800 mt-1" id="receiptDate"></div>
                </div>
                <div>
                    <div class="text-gray-500 uppercase tracking-wider">Type</div>
                    <div id="receiptType" class="mt-1"></div>
                </div>
            </div>
            
            <!-- Items Table -->
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">Items Purchased</h3>
                <table class="w-full text-gray-800 text-sm">
                    <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                        <tr>
                            <th class="py-2 px-3 text-left font-medium">Description</th>
                            <th class="py-2 px-3 text-right font-medium">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="receiptItems" class="divide-y divide-gray-200">
                        <!-- JS will populate this -->
                    </tbody>
                    <tfoot class="border-t-2 border-gray-200">
                        <tr>
                            <td class="pt-2 pb-1 px-3 text-right font-semibold text-gray-600">Total:</td>
                            <td class="pt-2 pb-1 px-3 text-right font-bold text-lg text-red-600" id="receiptAmount"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Footer Note -->
            <div class="text-xs text-gray-500 text-center mt-6 border-t border-gray-200 pt-3">
                Thank you for your purchase at Manila Total Fitness Center.
            </div>
        </div>
    </div>
</div> 