@extends('layouts.app')

@section('title', 'My Payments')

@section('head')
<style>
    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 50;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
        transition: all 0.3s ease;
    }
    
    .modal-content {
        background-color: #ffffff;
        margin: 10% auto;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .modal-open {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Card hover effects */
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">My Payments</h1>
        
        <a href="{{ route('profile') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center justify-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    </div>
    
    <!-- Filters Section (always visible) -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6 border border-gray-200">
        <form action="{{ route('user.payments') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Payment Type</label>
                <select id="type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">All Types</option>
                    <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Products</option>
                    <option value="subscription" {{ request('type') == 'subscription' ? 'selected' : '' }}>Subscriptions</option>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            
            <div class="sm:col-span-3 flex justify-end gap-3 mt-3">
                <a href="{{ route('user.payments') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>
    
    <!-- Invoices Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt #</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono text-gray-900">{{ Str::limit($invoice->invoice_number, 15) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $invoice->type === 'subscription' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($invoice->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-red-600">₱{{ number_format($invoice->total_amount, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center space-x-3">
                                <a href="{{ route('user.payment.details', $invoice->id) }}" 
                                   class="text-gray-600 hover:text-gray-900 transition"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="{{ route('user.payments.receipt', $invoice->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900 transition" title="Download Receipt">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-receipt text-4xl mb-3 text-gray-300"></i>
                                <p class="text-lg font-medium">No payments found</p>
                                <p class="text-sm mt-1">Your purchase and subscription payments will appear here.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $invoices->links() }}
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div id="paymentDetailsModal" class="modal">
    <div class="modal-content max-w-3xl bg-white border border-gray-200 rounded-lg shadow-lg">
        <div class="flex justify-between items-center mb-4 p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-gray-500"></i> 
                <span id="payment-details-title">Payment Details</span>
            </h2>
            <div class="flex items-center gap-2">
                <button id="print-payment-details" title="Download Receipt" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-download"></i>
                </button>
                <button onclick="closePaymentDetailsModal()" title="Close" class="text-gray-500 hover:text-gray-700 transition-colors text-lg">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <div id="payment-details-content" class="p-4 max-h-[70vh] overflow-y-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-sm">
                <div>
                    <div class="text-gray-500 uppercase tracking-wider">Receipt No.</div>
                    <div class="text-gray-800 font-mono mt-1" id="paymentDetailsInvoiceNumber"></div>
                </div>
                <div>
                    <div class="text-gray-500 uppercase tracking-wider">Date</div>
                    <div class="text-gray-800 mt-1" id="paymentDetailsDate"></div>
                </div>
                <div>
                    <div class="text-gray-500 uppercase tracking-wider">Type</div>
                    <div id="paymentDetailsType" class="mt-1"></div>
                </div>
                <div>
                    <div class="text-gray-500 uppercase tracking-wider">Status</div>
                    <div class="mt-1">
                        <span id="paymentDetailsStatus" class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Paid</span>
                    </div>
                </div>
            </div>
            
            <!-- Items Table -->
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">Items Purchased</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-gray-800 text-sm">
                        <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                            <tr>
                                <th class="py-2 px-3 text-left font-medium">Item</th>
                                <th class="py-2 px-3 text-left font-medium">Description</th>
                                <th class="py-2 px-3 text-center font-medium">Quantity</th>
                                <th class="py-2 px-3 text-right font-medium">Unit Price</th>
                                <th class="py-2 px-3 text-right font-medium">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="paymentDetailsItems" class="divide-y divide-gray-200">
                            <!-- JS will populate this -->
                        </tbody>
                        <tfoot class="border-t-2 border-gray-200">
                            <tr>
                                <td colspan="4" class="pt-3 pb-2 px-3 text-right font-semibold text-gray-600">Total:</td>
                                <td class="pt-3 pb-2 px-3 text-right font-bold text-lg text-red-600" id="paymentDetailsAmount"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <!-- Footer Note -->
            <div class="text-xs text-gray-500 text-center mt-6 border-t border-gray-200 pt-3">
                Thank you for your purchase at Manila Total Fitness Center.
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment Details Modal
    window.openPaymentDetailsModal = function(invoiceData, invoiceId) {
        // Set receipt details in the modal
        document.getElementById('payment-details-title').textContent = 'Payment #' + invoiceData.id.substring(0, 8);
        document.getElementById('paymentDetailsInvoiceNumber').textContent = invoiceData.id;
        document.getElementById('paymentDetailsDate').textContent = invoiceData.date;
        
        // Set type badge
        const typeElement = document.getElementById('paymentDetailsType');
        typeElement.innerHTML = '';
        const typeBadge = document.createElement('span');
        typeBadge.className = invoiceData.type.toLowerCase() === 'subscription' 
            ? 'px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium'
            : 'px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium';
        typeBadge.textContent = invoiceData.type;
        typeElement.appendChild(typeBadge);
        
        // Set amount
        document.getElementById('paymentDetailsAmount').textContent = '₱' + invoiceData.amount;
        
        // Set print button action
        document.getElementById('print-payment-details').onclick = function() {
            window.open('/my-payment/' + invoiceId + '/receipt', '_blank');
        };

        // Set items table content
        const itemsContainer = document.getElementById('paymentDetailsItems');
        itemsContainer.innerHTML = '';
        
        // Create rows for all items
        fetch('/api/invoice/' + invoiceId + '/items')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data.items.forEach(item => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';
                        
                        // Create image/product cell
                        const imageCell = document.createElement('td');
                        imageCell.className = 'py-3 px-3';
                        
                        if (item.product_image) {
                            const imgWrapper = document.createElement('div');
                            imgWrapper.className = 'flex items-center';
                            
                            const img = document.createElement('img');
                            img.src = item.product_image;
                            img.alt = item.description;
                            img.className = 'w-12 h-12 object-cover rounded-md mr-2';
                            
                            imgWrapper.appendChild(img);
                            imageCell.appendChild(imgWrapper);
                        } else {
                            const placeholder = document.createElement('div');
                            placeholder.className = 'w-12 h-12 bg-gray-200 rounded-md flex items-center justify-center';
                            
                            const icon = document.createElement('i');
                            icon.className = 'fas fa-box text-gray-400';
                            
                            placeholder.appendChild(icon);
                            imageCell.appendChild(placeholder);
                        }
                        
                        // Description cell
                        const descCell = document.createElement('td');
                        descCell.className = 'py-3 px-3';
                        descCell.textContent = item.description;
                        
                        // Quantity cell
                        const qtyCell = document.createElement('td');
                        qtyCell.className = 'py-3 px-3 text-center';
                        qtyCell.textContent = item.quantity || 1;
                        
                        // Unit price cell
                        const unitPriceCell = document.createElement('td');
                        unitPriceCell.className = 'py-3 px-3 text-right';
                        unitPriceCell.textContent = '₱' + (item.unit_price ? parseFloat(item.unit_price).toFixed(2) : parseFloat(item.amount).toFixed(2));
                        
                        // Amount cell
                        const amountCell = document.createElement('td');
                        amountCell.className = 'py-3 px-3 text-right font-medium';
                        amountCell.textContent = '₱' + parseFloat(item.amount).toFixed(2);
                        
                        // Add all cells to row
                        row.appendChild(imageCell);
                        row.appendChild(descCell);
                        row.appendChild(qtyCell);
                        row.appendChild(unitPriceCell);
                        row.appendChild(amountCell);
                        
                        // Add row to table
                        itemsContainer.appendChild(row);
                    });
                } else {
                    // Simple fallback for when API isn't available
                    invoiceData.items.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="py-3 px-3">
                                <div class="w-12 h-12 bg-gray-200 rounded-md flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400"></i>
                                </div>
                            </td>
                            <td class="py-3 px-3">${item.description}</td>
                            <td class="py-3 px-3 text-center">1</td>
                            <td class="py-3 px-3 text-right">₱${parseFloat(item.amount).toFixed(2)}</td>
                            <td class="py-3 px-3 text-right font-medium">₱${parseFloat(item.amount).toFixed(2)}</td>
                        `;
                        itemsContainer.appendChild(row);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching invoice details:', error);
                // Fallback to simple display
                invoiceData.items.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="py-3 px-3">
                            <div class="w-12 h-12 bg-gray-200 rounded-md flex items-center justify-center">
                                <i class="fas fa-box text-gray-400"></i>
                            </div>
                        </td>
                        <td class="py-3 px-3">${item.description}</td>
                        <td class="py-3 px-3 text-center">1</td>
                        <td class="py-3 px-3 text-right">₱${parseFloat(item.amount).toFixed(2)}</td>
                        <td class="py-3 px-3 text-right font-medium">₱${parseFloat(item.amount).toFixed(2)}</td>
                    `;
                    itemsContainer.appendChild(row);
                });
            });
        
        // Show the modal
        document.getElementById('paymentDetailsModal').classList.add('modal-open');
    };

    window.closePaymentDetailsModal = function() {
        document.getElementById('paymentDetailsModal').classList.remove('modal-open');
    };
});
</script>
@endsection 