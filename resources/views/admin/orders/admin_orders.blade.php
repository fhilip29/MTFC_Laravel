@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4">
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
            <h2 class="text-3xl font-bold text-gray-800">📦 Manage Orders</h2>
            <div class="relative w-full sm:w-80">
                <input 
                    type="text" 
                    placeholder="Search orders..." 
                    class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
                >
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 text-base">
                <thead class="bg-gray-100 text-gray-600 uppercase text-sm">
                    <tr>
                        <th class="px-6 py-3 text-left">Order ID</th>
                        <th class="px-6 py-3 text-left">Order Date</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
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
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800">{{ $order['id'] }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $order['date'] }}</td>
                            <td class="px-6 py-4">
                                <span 
                                    class="inline-flex items-center gap-2 text-white px-3 py-1 rounded-full text-sm font-medium {{ $colors[$order['status']] }}"
                                    title="{{ $order['status'] }}"
                                >
                                    <i class="{{ $icons[$order['status']] }}"></i> {{ $order['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button 
                                    title="View Details"
                                    class="inline-flex items-center px-3 py-2 text-sm font-semibold text-blue-600 border border-blue-600 rounded-md hover:bg-blue-50 transition"
                                >
                                    <i class="fas fa-eye mr-2"></i> View
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
