@extends('layouts.admin')

@section('content')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mx-auto px-2 sm:px-4">
    <div class="bg-[#1F2937] shadow-lg rounded-xl p-4 sm:p-6 border border-[#374151]">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 gap-4">
            <h2 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-2"><i class="fas fa-shopping-cart text-[#9CA3AF]"></i> Manage Orders</h2>
            <div class="relative w-full sm:w-80">
                <input 
                    type="text" 
                    id="orderSearch"
                    placeholder="Search orders..." 
                    class="w-full px-4 py-2 pl-10 bg-[#374151] border border-[#4B5563] text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] text-sm sm:text-base" 
                >
                <i class="fas fa-search absolute left-3 top-3 text-[#9CA3AF]"></i>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-sm -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
            <table class="min-w-full divide-y divide-[#374151] text-sm sm:text-base">
                <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-sm">
                    <tr>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left">Order ID</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left">Customer</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left">Order Date</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left">Status</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-[#1F2937] divide-y divide-[#374151]">
                    @php
                        $colors = [
                            'Accepted' => 'bg-green-500',
                            'Pending' => 'bg-yellow-400',
                            'Cancelled' => 'bg-red-500',
                            'Completed' => 'bg-emerald-600',
                            'Out for Delivery' => 'bg-blue-500',
                        ];

                        $icons = [
                            'Accepted' => 'fas fa-check-circle',
                            'Pending' => 'fas fa-clock',
                            'Cancelled' => 'fas fa-times-circle',
                            'Completed' => 'fas fa-check-double',
                            'Out for Delivery' => 'fas fa-truck',
                        ];
                    @endphp

                    @forelse($orders as $order)
                        <tr class="hover:bg-[#374151] transition order-row">
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap font-medium text-white text-xs sm:text-base order-id">{{ $order->reference_no }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-[#9CA3AF] text-xs sm:text-base">{{ $order->first_name }} {{ $order->last_name }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-[#9CA3AF] text-xs sm:text-base">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <span 
                                    class="inline-flex items-center gap-2 text-white px-3 py-1 rounded-full text-sm font-medium {{ $colors[$order->status] ?? 'bg-gray-500' }}"
                                    title="{{ $order->status }}"
                                >
                                    <i class="{{ $icons[$order->status] ?? 'fas fa-question-circle' }}"></i> {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <div class="flex space-x-2">
                                    <button 
                                        onclick="viewOrderDetails('{{ $order->id }}')"
                                        title="View Details"
                                        class="inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold text-white border border-[#4B5563] rounded-md hover:bg-[#374151] hover:text-white transition"
                                    >
                                        <i class="fas fa-eye mr-1 sm:mr-2"></i> <span class="hidden sm:inline">View</span>
                                    </button>
                                    <button 
                                        onclick="updateOrderStatus('{{ $order->id }}', '{{ $order->reference_no }}', '{{ $order->status }}')"
                                        title="Update Status"
                                        class="inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold text-white border border-[#4B5563] rounded-md hover:bg-[#374151] hover:text-white transition"
                                    >
                                        <i class="fas fa-edit mr-1 sm:mr-2"></i> <span class="hidden sm:inline">Status</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 sm:px-6 py-4 text-center text-[#9CA3AF]">
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <!-- Pagination Links if needed -->
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>

<!-- Order Details Modal (Hidden by default) -->
<div id="orderDetailsModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-[#1F2937] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[#374151]">
            <!-- Modal content will be loaded here -->
            <div id="orderDetailsContent" class="p-6">
                <div class="flex justify-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white"></div>
                </div>
            </div>
            <div class="bg-[#374151] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeOrderDetailsModal()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Filter orders by search term
    document.getElementById('orderSearch').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const orderRows = document.querySelectorAll('.order-row');
        
        orderRows.forEach(row => {
            const orderId = row.querySelector('.order-id').textContent.toLowerCase();
            const isVisible = orderId.includes(searchTerm);
            row.classList.toggle('hidden', !isVisible);
        });
    });

    // Function to view order details
    function viewOrderDetails(orderId) {
        // Show modal
        const modal = document.getElementById('orderDetailsModal');
        modal.classList.remove('hidden');
        
        // Show loading spinner
        document.getElementById('orderDetailsContent').innerHTML = `
            <div class="flex justify-center">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white"></div>
            </div>
        `;
        
        // Fetch order details
        fetch(`/admin/orders/${orderId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const order = data.order;
                    let itemsHtml = '';
                    
                    // Generate HTML for order items
                    order.items.forEach(item => {
                        itemsHtml += `
                            <div class="flex items-center space-x-4 border-b border-[#374151] pb-4 mb-4">
                                <img src="${item.product.image}" alt="${item.product.name}" class="w-16 h-16 object-cover rounded">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-white">${item.product.name}</h4>
                                    <div class="text-sm text-[#9CA3AF]">
                                        <span>Qty: ${item.quantity}</span>
                                        <span class="mx-2">|</span>
                                        <span>₱${item.price * item.quantity}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    // Calculate total
                    const total = order.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                    
                    // Update modal content
                    document.getElementById('orderDetailsContent').innerHTML = `
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-white">Order #${order.reference_no}</h3>
                                <span class="px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(order.status)}">
                                    ${order.status}
                                </span>
                            </div>
                            
                            <div class="border-t border-[#374151] pt-4">
                                <h4 class="text-sm font-semibold text-white mb-2">Customer Details</h4>
                                <p class="text-sm text-[#9CA3AF]">${order.first_name} ${order.last_name}</p>
                                <p class="text-sm text-[#9CA3AF]">${order.phone_number}</p>
                            </div>
                            
                            <div class="border-t border-[#374151] pt-4">
                                <h4 class="text-sm font-semibold text-white mb-2">Shipping Address</h4>
                                <p class="text-sm text-[#9CA3AF]">${order.street}, ${order.barangay}</p>
                                <p class="text-sm text-[#9CA3AF]">${order.city}, ${order.postal_code}</p>
                            </div>
                            
                            <div class="border-t border-[#374151] pt-4">
                                <h4 class="text-sm font-semibold text-white mb-2">Payment Method</h4>
                                <p class="text-sm text-[#9CA3AF]">${order.payment_method}</p>
                            </div>
                            
                            <div class="border-t border-[#374151] pt-4">
                                <h4 class="text-sm font-semibold text-white mb-2">Order Items</h4>
                                <div class="space-y-4">
                                    ${itemsHtml}
                                </div>
                            </div>
                            
                            <div class="border-t border-[#374151] pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-white">Total:</span>
                                    <span class="text-sm font-bold text-white">₱${total.toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    document.getElementById('orderDetailsContent').innerHTML = `
                        <div class="text-center text-red-500">
                            <p>Error loading order details.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('orderDetailsContent').innerHTML = `
                    <div class="text-center text-red-500">
                        <p>Error loading order details.</p>
                    </div>
                `;
            });
    }
    
    // Close order details modal
    function closeOrderDetailsModal() {
        document.getElementById('orderDetailsModal').classList.add('hidden');
    }
    
    // Update order status
    function updateOrderStatus(orderId, referenceNo, currentStatus) {
        Swal.fire({
            title: `Update Order Status`,
            html: `
                <div class="text-left mb-4">
                    <p class="mb-2">Order: <strong>${referenceNo}</strong></p>
                    <p class="mb-4">Current Status: <strong>${currentStatus}</strong></p>
                    <label for="status-select" class="block text-sm font-medium text-gray-700 mb-1">New Status:</label>
                    <select id="status-select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
                        <option value="Pending" ${currentStatus === 'Pending' ? 'selected' : ''}>Pending</option>
                        <option value="Accepted" ${currentStatus === 'Accepted' ? 'selected' : ''}>Accepted</option>
                        <option value="Out for Delivery" ${currentStatus === 'Out for Delivery' ? 'selected' : ''}>Out for Delivery</option>
                        <option value="Completed" ${currentStatus === 'Completed' ? 'selected' : ''}>Completed</option>
                        <option value="Cancelled" ${currentStatus === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Update Status',
            cancelButtonText: 'Cancel',
            focusConfirm: false,
            preConfirm: () => {
                return {
                    status: document.getElementById('status-select').value
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const newStatus = result.value.status;
                
                // Show loading
                Swal.fire({
                    title: 'Updating Status',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send request to update status
                fetch(`/admin/orders/${orderId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Updated',
                            text: `Order status has been updated to ${newStatus}`,
                            confirmButtonColor: '#10B981'
                        }).then(() => {
                            // Reload the page to show updated data
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to update order status',
                            confirmButtonColor: '#EF4444'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the order status',
                        confirmButtonColor: '#EF4444'
                    });
                });
            }
        });
    }
    
    // Helper function to get status color class
    function getStatusColor(status) {
        const colors = {
            'Accepted': 'bg-green-500 text-white',
            'Pending': 'bg-yellow-400 text-gray-800',
            'Cancelled': 'bg-red-500 text-white',
            'Completed': 'bg-emerald-600 text-white',
            'Out for Delivery': 'bg-blue-500 text-white'
        };
        
        return colors[status] || 'bg-gray-500 text-white';
    }
</script>
@endsection
