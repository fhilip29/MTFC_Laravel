@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
    $stats = [
        ['label' => 'New Visitor', 'count' => 8, 'icon' => 'fas fa-user-plus', 'color' => 'bg-blue-500'],
        ['label' => 'New Members', 'count' => 4, 'icon' => 'fas fa-users', 'color' => 'bg-green-500'],
        ['label' => 'Expired Membership', 'count' => 0, 'icon' => 'fas fa-clock', 'color' => 'bg-yellow-500'],
        ['label' => 'Cancelled Membership', 'count' => 0, 'icon' => 'fas fa-times-circle', 'color' => 'bg-red-500'],
    ];

    $orders = [
        ['id' => '67fb3540086200e3004a8000', 'date' => '2025-04-13', 'status' => 'Accepted', 'color' => 'bg-green-500'],
        ['id' => '67a56af95f616afe1db8f09', 'date' => '2025-04-12', 'status' => 'Pending', 'color' => 'bg-gray-500'],
        ['id' => '67f1e208ccc16bbe38bce1cb', 'date' => '2025-04-06', 'status' => 'Cancelled', 'color' => 'bg-red-500'],
        ['id' => '67f0ec29da9e8fa68e4f90f8', 'date' => '2025-04-05', 'status' => 'Completed', 'color' => 'bg-green-500'],
        ['id' => '67ee504632235b11cf6e61b4', 'date' => '2025-04-03', 'status' => 'Accepted', 'color' => 'bg-green-500'],
    ];

    $feedback = [
        ['name' => 'Robi', 'msg' => 'poser'],
        ['name' => 'Fhilip KR Lorenzo', 'msg' => 'Hello Hi goodbye'],
        ['name' => 'Fhilip KR Lorenzo', 'msg' => 'Hello'],
    ];
@endphp

{{-- Stat Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @foreach($stats as $stat)
    <div class="bg-white p-5 rounded-2xl shadow-md flex items-center space-x-4 hover:shadow-lg transition">
        <div class="{{ $stat['color'] }} text-white p-3 rounded-full">
            <i class="{{ $stat['icon'] }}"></i>
        </div>
        <div>
            <div class="text-sm text-gray-500">{{ $stat['label'] }}</div>
            <div class="text-2xl font-bold">{{ $stat['count'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Charts + Orders --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Sales Chart --}}
    <div class="bg-white p-6 rounded-2xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Sales Report</h2>
        <canvas id="salesChart" class="w-full h-64"></canvas>
    </div>

    {{-- Orders --}}
    <div class="bg-white p-6 rounded-2xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Latest Orders</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm table-auto">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="py-3 px-4">Order ID</th>
                        <th class="py-3 px-4">Order Date</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="py-3 px-4">{{ $order['id'] }}</td>
                        <td class="py-3 px-4">{{ $order['date'] }}</td>
                        <td class="py-3 px-4">
                            <span class="text-white px-2 py-1 rounded text-xs {{ $order['color'] }}">
                                {{ $order['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Feedback Table --}}
<div class="bg-white p-6 mt-8 rounded-2xl shadow-md">
    <h2 class="text-lg font-semibold mb-4">Client Feedback</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm table-auto">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="py-3 px-4">Client</th>
                    <th class="py-3 px-4">Message</th>
                    <th class="py-3 px-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach($feedback as $item)
                <tr class="hover:bg-gray-50 border-b">
                    <td class="py-3 px-4">{{ $item['name'] }}</td>
                    <td class="py-3 px-4">{{ $item['msg'] }}</td>
                    <td class="py-3 px-4 text-center text-blue-600 hover:text-blue-800 cursor-pointer">
                        <i class="fas fa-eye"></i>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Sales',
                data: [0, 0, 0, 1400, 0, 0],
                borderColor: 'rgba(59,130,246,1)',
                backgroundColor: 'rgba(59,130,246,0.15)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(59,130,246,1)',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#4B5563',
                        font: { size: 12 }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#6B7280' },
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#6B7280' },
                    grid: { color: '#E5E7EB' }
                }
            }
        }
    });
</script>
@endsection
