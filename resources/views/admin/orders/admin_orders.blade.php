@extends('layouts.admin')

@section('content')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mx-auto px-2 sm:px-4">
    <div class="bg-[#1F2937] shadow-lg rounded-xl p-4 sm:p-6 border border-[#374151]">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 gap-4">
            <h2 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-2"><i class="fas fa-shopping-cart text-[#9CA3AF]"></i> Manage Orders</h2>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-80">
                    <input 
                        type="text" 
                        id="orderSearch"
                        placeholder="Search orders..." 
                        class="w-full px-4 py-2 pl-10 bg-[#374151] border border-[#4B5563] text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] text-sm sm:text-base" 
                    >
                    <i class="fas fa-search absolute left-3 top-3 text-[#9CA3AF]"></i>
                </div>
                <div class="relative w-full sm:w-44">
                    <select 
                        id="statusFilter" 
                        class="w-full px-4 py-2 pr-10 bg-[#374151] border border-[#4B5563] text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] appearance-none text-sm sm:text-base"
                    >
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Accepted">Accepted</option>
                        <option value="Out for Delivery">Out for Delivery</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                    <i class="fas fa-filter absolute right-3 top-3 text-[#9CA3AF]"></i>
                </div>
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
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-center w-28">Status</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-center w-32">Action</th>
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
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-center w-28">
                                <span 
                                    class="inline-flex items-center justify-center gap-1 text-white px-2 py-1 rounded-full text-xs font-medium {{ $colors[$order->status] ?? 'bg-gray-500' }}"
                                    title="{{ $order->status }}"
                                >
                                    <i class="{{ $icons[$order->status] ?? 'fas fa-question-circle' }}"></i> {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-center w-32">
                                <div class="flex items-center justify-center space-x-2">
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
    <div class="flex items-center justify-center min-h-screen p-4">
        <!-- Modal backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeOrderDetailsModal()"></div>
        
        <!-- Modal content -->
        <div class="relative bg-[#1F2937] rounded-lg max-w-2xl w-full mx-auto shadow-xl z-50 border border-[#374151]">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b border-[#374151]">
                <h3 class="text-xl font-bold text-white" id="modal-title">Order Details</h3>
                <button onclick="closeOrderDetailsModal()" class="text-[#9CA3AF] hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Modal body with content -->
            <div class="p-6 max-h-[70vh] overflow-y-auto" id="orderDetailsContent">
                <div class="flex justify-center items-center h-40">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Filter orders by search term and status
    function filterOrders() {
        const searchTerm = document.getElementById('orderSearch').value.toLowerCase();
        const selectedStatus = document.getElementById('statusFilter').value;
        const orderRows = document.querySelectorAll('.order-row');
        
        orderRows.forEach(row => {
            const orderId = row.querySelector('.order-id').textContent.toLowerCase();
            const statusElement = row.querySelector('td:nth-child(4) span');
            const status = statusElement ? statusElement.textContent.trim() : '';
            
            const matchesSearch = orderId.includes(searchTerm);
            const matchesStatus = selectedStatus === '' || status === selectedStatus;
            
            row.classList.toggle('hidden', !(matchesSearch && matchesStatus));
        });
    }
    
    // Add event listeners for both filters
    document.getElementById('orderSearch').addEventListener('keyup', filterOrders);
    document.getElementById('statusFilter').addEventListener('change', filterOrders);

    // Function to view order details
    function viewOrderDetails(orderId) {
        // Show modal
        const modal = document.getElementById('orderDetailsModal');
        modal.classList.remove('hidden');
        
        // Show loading spinner
        document.getElementById('orderDetailsContent').innerHTML = `
            <div class="flex justify-center items-center h-40">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
            </div>
        `;
        
        // Update modal title
        document.getElementById('modal-title').textContent = 'Loading Order Details...';
        
        // Fetch order details
        fetch(`/admin/orders/${orderId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const order = data.order;
                    
                    // Update modal title
                    document.getElementById('modal-title').textContent = `Order #${order.reference_no}`;
                    
                    // Build the order items HTML
                    let itemsHtml = '';
                    const total = order.items.reduce((sum, item) => {
                        const itemTotal = item.price * item.quantity;
                        
                        itemsHtml += `
                            <div class="flex items-start space-x-3 pb-3 mb-3 border-b border-[#374151]">
                                <img src="${item.product.image}" alt="${item.product.name}" class="w-16 h-16 object-cover rounded">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-medium">${item.product.name}</p>
                                    <div class="text-sm text-[#9CA3AF] mt-1">
                                        <span>${item.quantity} × ₱${parseFloat(item.price).toFixed(2)}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-white font-medium">₱${(item.price * item.quantity).toFixed(2)}</span>
                                </div>
                            </div>
                        `;
                        
                        return sum + itemTotal;
                    }, 0);
                    
                    // Get formatted date
                    const orderDate = new Date(order.created_at);
                    const formattedDate = orderDate.toLocaleDateString('en-US', {
                        year: 'numeric', month: 'short', day: 'numeric'
                    });
                    
                    // Get status with appropriate styling
                    const colors = {
                        'Accepted': 'bg-green-500',
                        'Pending': 'bg-yellow-400',
                        'Cancelled': 'bg-red-500',
                        'Completed': 'bg-emerald-600',
                        'Out for Delivery': 'bg-blue-500',
                    };
                    
                    const icons = {
                        'Accepted': 'fas fa-check-circle',
                        'Pending': 'fas fa-clock',
                        'Cancelled': 'fas fa-times-circle',
                        'Completed': 'fas fa-check-double',
                        'Out for Delivery': 'fas fa-truck',
                    };
                    
                    const statusColor = colors[order.status] || 'bg-gray-500';
                    const statusIcon = icons[order.status] || 'fas fa-question-circle';
                    
                    // Build the complete modal content
                    const content = `
                        <div class="space-y-6">
                            <!-- Status and Date -->
                            <div class="flex justify-between items-center">
                                <span class="text-[#9CA3AF]">
                                    <i class="far fa-calendar-alt mr-1"></i> ${formattedDate}
                                </span>
                                <span class="inline-flex items-center gap-1 text-white px-3 py-1 rounded-full text-sm font-medium ${statusColor}">
                                    <i class="${statusIcon}"></i> ${order.status}
                                </span>
                            </div>
                            
                            <!-- Customer Info and Shipping -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-[#111827] p-4 rounded-lg border border-[#374151]">
                                <div>
                                    <h4 class="text-sm font-bold text-white uppercase mb-2">Customer</h4>
                                    <p class="text-[#9CA3AF] mb-1">${order.first_name} ${order.last_name}</p>
                                    <p class="text-[#9CA3AF]">${order.phone_number}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-white uppercase mb-2">Ship To</h4>
                                    <p class="text-[#9CA3AF] mb-1">${order.street}, ${order.barangay}</p>
                                    <p class="text-[#9CA3AF]">${order.city}, ${order.postal_code}</p>
                                </div>
                            </div>
                            
                            <!-- Payment Method -->
                            <div>
                                <h4 class="text-sm font-bold text-white uppercase mb-2">Payment Method</h4>
                                <p class="text-[#9CA3AF]">${order.payment_method}</p>
                            </div>
                            
                            <!-- Order Items -->
                            <div>
                                <h4 class="text-sm font-bold text-white uppercase mb-3">Order Items</h4>
                                <div class="space-y-3 bg-[#111827] p-4 rounded-lg border border-[#374151]">
                                    ${itemsHtml}
                                    <div class="flex justify-between pt-3 border-t border-[#374151]">
                                        <span class="font-bold text-white">Total</span>
                                        <span class="font-bold text-white">₱${total.toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex justify-end space-x-3 pt-2">
                                <button onclick="updateOrderStatus('${order.id}', '${order.reference_no}', '${order.status}')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-edit mr-1"></i> Update Status
                                </button>
                                <button onclick="closeOrderDetailsModal()" class="px-4 py-2 bg-[#374151] text-white rounded hover:bg-[#4B5563] transition-colors">
                                    Close
                                </button>
                            </div>
                        </div>
                    `;
                    
                    // Update modal content
                    document.getElementById('orderDetailsContent').innerHTML = content;
                    
                } else {
                    document.getElementById('orderDetailsContent').innerHTML = `
                        <div class="text-center text-red-500 py-8">
                            <i class="fas fa-exclamation-circle text-3xl mb-3"></i>
                            <p>Error loading order details.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('orderDetailsContent').innerHTML = `
                    <div class="text-center text-red-500 py-8">
                        <i class="fas fa-exclamation-circle text-3xl mb-3"></i>
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
</script>
@endsection
