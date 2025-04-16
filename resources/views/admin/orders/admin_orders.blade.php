@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-2 sm:px-4">
    <div class="bg-[#1F2937] shadow-lg rounded-xl p-4 sm:p-6 border border-[#374151]">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 gap-4">
            <h2 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-2"><i class="fas fa-shopping-cart text-[#9CA3AF]"></i> Manage Orders</h2>
            <div class="relative w-full sm:w-80">
                <input 
                    type="text" 
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
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left">Order Date</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left">Status</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-[#1F2937] divide-y divide-[#374151]">
                    @php
                        $orders = [
                            ['id' => '67fb3540086200c3004a8000', 'date' => '2025-04-13', 'status' => 'Accepted'],
                            ['id' => '67fa56af95f616afe1db8f09', 'date' => '2025-04-12', 'status' => 'Pending'],
                            ['id' => '67f1e208ccc16bbe38bce1cb', 'date' => '2025-04-06', 'status' => 'Cancelled'],
                            ['id' => '67f0ec29da9e6fa68e4f90f8', 'date' => '2025-04-05', 'status' => 'Completed'],
                            ['id' => '67ee504632235b11cf6e61b4', 'date' => '2025-04-03', 'status' => 'Accepted'],
                            ['id' => '67ee31d5d8d6da4022bba2c5', 'date' => '2025-04-03', 'status' => 'Out for Delivery'],
                            ['id' => '67eb368a40ad2ac6182106dd', 'date' => '2025-04-01', 'status' => 'Pending'],
                        ];

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

                    @foreach($orders as $order)
                        <tr class="hover:bg-[#374151] transition">
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap font-medium text-white text-xs sm:text-base">{{ $order['id'] }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-[#9CA3AF] text-xs sm:text-base">{{ $order['date'] }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <span 
                                    class="inline-flex items-center gap-2 text-white px-3 py-1 rounded-full text-sm font-medium {{ $colors[$order['status']] }}"
                                    title="{{ $order['status'] }}"
                                >
                                    <i class="{{ $icons[$order['status']] }}"></i> {{ $order['status'] }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <button 
                                    title="View Details"
                                    class="inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold text-white border border-[#4B5563] rounded-md hover:bg-[#374151] hover:text-white transition"
                                >
                                    <i class="fas fa-eye mr-1 sm:mr-2"></i> <span class="hidden sm:inline">View</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection
