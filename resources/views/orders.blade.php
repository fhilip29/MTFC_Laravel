@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="{ showModal: false, selectedOrder: null }">
    <h1 class="text-3xl font-bold mb-8 text-white">My Orders</h1>
    
    <div class="bg-[#111827] rounded-lg shadow overflow-hidden border border-[#374151]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#374151]">
                <thead class="bg-[#374151]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#9CA3AF] uppercase tracking-wider">Reference No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#9CA3AF] uppercase tracking-wider">Order Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#9CA3AF] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#9CA3AF] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-[#111827] divide-y divide-[#374151]">
                    <!-- Sample Order Data - Replace with actual data from database -->
                    <tr class="hover:bg-[#374151] transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">67fb354008620e3004a8000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#9CA3AF]">2025-04-13</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-[#374151] text-[#9CA3AF]">Pending</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#9CA3AF]">
                            <button @click="showModal = true; selectedOrder = { id: '67fb354008620e3004a8000', date: '2025-04-13', status: 'Pending', progress: 25, items: [{ name: 'All Max Classic All Whey', image: '/assets/Product2_MTFC.jpg', qty: 1, price: 3000 }], total: 3000, address: '123 Main St, City, Country' }" class="text-[#9CA3AF] hover:text-white focus:outline-none transition duration-150 ease-in-out">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#374151] transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">67f1e208cc16bbe38bce1cb</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#9CA3AF]">2025-04-06</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-[#374151] text-red-400">Cancelled</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#9CA3AF]">
                            <button @click="showModal = true; selectedOrder = { id: '67f1e208cc16bbe38bce1cb', date: '2025-04-06', status: 'Cancelled', progress: 0, items: [{ name: 'All Max Classic All Whey', image: '/assets/Product2_MTFC.jpg', qty: 2, price: 6000 }], total: 6000, address: '456 Oak St, City, Country' }" class="text-[#9CA3AF] hover:text-white focus:outline-none transition duration-150 ease-in-out">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#374151] transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">67f0ec29da9e8fa68e4f90f8</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#9CA3AF]">2025-04-05</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-[#374151] text-green-400">Completed</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#9CA3AF]">
                            <button @click="showModal = true; selectedOrder = { id: '67f0ec29da9e8fa68e4f90f8', date: '2025-04-05', status: 'Completed', progress: 100, items: [{ name: 'All Max Classic All Whey', image: '/assets/Product2_MTFC.jpg', qty: 3, price: 9000 }], total: 9000, address: '789 Pine St, City, Country' }" class="text-[#9CA3AF] hover:text-white focus:outline-none transition duration-150 ease-in-out">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div x-show="showModal" class="fixed inset-0 overflow-y-auto z-50" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-[#111827] opacity-90"></div>
            </div>

            <div class="inline-block align-bottom bg-[#111827] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[#374151]">
                <div class="bg-[#111827] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-white mb-4" x-text="'Order #' + selectedOrder?.id"></h3>
                            
                            <!-- Order Progress -->
                            <div class="mb-6">
                                <div class="relative pt-1">
                                    <div class="flex mb-2 items-center justify-between">
                                        <div>
                                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full" :class="{
                                                'text-green-400 bg-[#374151]': selectedOrder?.status === 'Completed',
                                                'text-[#9CA3AF] bg-[#374151]': selectedOrder?.status === 'Pending',
                                                'text-red-400 bg-[#374151]': selectedOrder?.status === 'Cancelled'
                                            }" x-text="selectedOrder?.status"></span>
                                        </div>
                                    </div>
                                    <div class="flex h-2 mb-4 overflow-hidden bg-[#374151] rounded">
                                        <div class="flex flex-col justify-center overflow-hidden bg-green-500" role="progressbar" :style="`width: ${selectedOrder?.progress}%`" :aria-valuenow="selectedOrder?.progress" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="space-y-4">
                                <template x-for="item in selectedOrder?.items" :key="item.name">
                                    <div class="flex items-center space-x-4 border-b border-[#374151] pb-4">
                                        <img :src="item.image" :alt="item.name" class="w-16 h-16 object-cover rounded">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-white" x-text="item.name"></h4>
                                            <div class="text-sm text-[#9CA3AF]">
                                                <span x-text="'Qty: ' + item.qty"></span>
                                                <span class="mx-2">|</span>
                                                <span x-text="'₱' + item.price"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Order Summary -->
                            <div class="mt-4 pt-4 border-t border-[#374151]">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-white">Total:</span>
                                    <span class="font-bold text-white" x-text="'₱' + selectedOrder?.total"></span>
                                </div>
                                <div class="mt-2 text-sm text-[#9CA3AF]">
                                    <div class="font-medium">Delivery Address:</div>
                                    <div x-text="selectedOrder?.address"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-[#374151] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="showModal = false" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection