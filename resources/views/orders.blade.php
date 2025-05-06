@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="{ showModal: false, selectedOrder: null }">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">My Orders</h1>
    
    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->reference_no }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->order_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($order->status == 'Completed') bg-green-100 text-green-800
                                @elseif($order->status == 'Cancelled') bg-red-100 text-red-800
                                @elseif($order->status == 'Out for Delivery') bg-blue-100 text-blue-800
                                @elseif($order->status == 'Accepted') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center space-x-3">
                                <button @click="showModal = true; selectedOrder = { 
                                    id: '{{ $order->reference_no }}', 
                                    date: '{{ $order->order_date->format('M d, Y') }}', 
                                    status: '{{ $order->status }}', 
                                    progress: @if($order->status == 'Completed') 100 
                                              @elseif($order->status == 'Cancelled') 0 
                                              @elseif($order->status == 'Out for Delivery') 75 
                                              @elseif($order->status == 'Accepted') 50 
                                              @else 25 @endif, 
                                    items: {{ Illuminate\Support\Js::from($order->items->map(function($item) { 
                                        return [
                                            'name' => $item->product->name, 
                                            'image' => $item->product->image, 
                                            'qty' => $item->quantity, 
                                            'price' => $item->price * $item->quantity,
                                            'discount' => $item->product->discount ?? 0
                                        ]; 
                                    })) }},
                                    total: {{ $order->items->sum(function($item) { return $item->price * $item->quantity; }) }},
                                    address: '{{ $order->street }}, {{ $order->barangay }}, {{ $order->city }}, {{ $order->postal_code }}'
                                }" class="text-gray-600 hover:text-gray-900 focus:outline-none transition duration-150 ease-in-out">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                
                                @if($order->status == 'Pending')
                                    <button onclick="confirmCancelOrder('{{ $order->id }}', '{{ $order->reference_no }}')" class="text-red-600 hover:text-red-800 focus:outline-none transition duration-150 ease-in-out">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            No orders found. <a href="{{ route('shop') }}" class="text-red-600 hover:text-red-800">Start shopping</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div x-show="showModal" class="fixed inset-0 overflow-y-auto z-50" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" x-text="'Order #' + selectedOrder?.id"></h3>
                            
                            <!-- Order Progress -->
                            <div class="mb-6">
                                <div class="relative pt-1">
                                    <div class="flex mb-2 items-center justify-between">
                                        <div>
                                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full" :class="{
                                                'text-green-800 bg-green-100': selectedOrder?.status === 'Completed',
                                                'text-gray-800 bg-gray-100': selectedOrder?.status === 'Pending',
                                                'text-red-800 bg-red-100': selectedOrder?.status === 'Cancelled',
                                                'text-blue-800 bg-blue-100': selectedOrder?.status === 'Out for Delivery',
                                                'text-yellow-800 bg-yellow-100': selectedOrder?.status === 'Accepted'
                                            }" x-text="selectedOrder?.status"></span>
                                        </div>
                                    </div>
                                    <div class="flex h-2 mb-4 overflow-hidden bg-gray-200 rounded">
                                        <div class="flex flex-col justify-center overflow-hidden bg-green-500" role="progressbar" :style="`width: ${selectedOrder?.progress}%`" :aria-valuenow="selectedOrder?.progress" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="space-y-4">
                                <template x-for="item in selectedOrder?.items" :key="item.name">
                                    <div class="flex items-center space-x-4 border-b border-gray-200 pb-4">
                                        <img :src="item.image" :alt="item.name" class="w-16 h-16 object-cover rounded">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-gray-900" x-text="item.name"></h4>
                                            <div class="text-sm text-gray-500">
                                                <span x-text="'Qty: ' + item.qty"></span>
                                                <span class="mx-2">|</span>
                                                <template x-if="item.discount > 0">
                                                    <span>
                                                        <span class="line-through text-gray-400" x-text="'₱' + (item.price / (1 - item.discount/100)).toFixed(2)"></span>
                                                        <span class="text-red-600 font-medium ml-1" x-text="'₱' + item.price"></span>
                                                        <span class="bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded-full ml-1" x-text="item.discount + '% OFF'"></span>
                                                    </span>
                                                </template>
                                                <template x-if="!item.discount">
                                                    <span x-text="'₱' + item.price"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Order Summary -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900">Total:</span>
                                    <span class="font-bold text-gray-900" x-text="'₱' + selectedOrder?.total"></span>
                                </div>
                                <div class="mt-2 text-sm text-gray-500">
                                    <div class="font-medium">Delivery Address:</div>
                                    <div x-text="selectedOrder?.address"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="showModal = false" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Display session messages with SweetAlert
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    });

    function confirmCancelOrder(orderId, referenceNo) {
        Swal.fire({
            title: 'Cancel Order?',
            text: `Are you sure you want to cancel order #${referenceNo}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/orders/${orderId}/cancel`;
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection