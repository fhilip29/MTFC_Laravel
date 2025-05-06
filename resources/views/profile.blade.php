@extends('layouts.app')

@section('title', 'User Profile')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<style>
    /* Animation for sidebar toggle icon */
    .rotate-icon {
        transition: transform 0.3s ease;
    }
    
    .rotate-icon-open {
        transform: rotate(180deg);
    }
    
    /* QR Code Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 50;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.8);
        transition: all 0.3s ease;
    }
    
    .modal-content {
        background-color: #2d2d2d;
        margin: 10% auto;
        padding: 25px;
        border-radius: 12px;
        max-width: 400px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.5);
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
        box-shadow: 0 10px 15px rgba(0,0,0,0.3);
    }
</style>
@endsection

@section('content')
@php
    // Default value to prevent undefined variable error
    $activeSubscription = $activeSubscription ?? null;
    $invoices = $invoices ?? collect();
@endphp

<div class="flex flex-col md:flex-row min-h-screen bg-[#121212] text-white">
    <!-- Mobile Toggle for Sidebar - Moved lower -->
    <button id="sidebarToggle" class="md:hidden bg-[#2d2d2d] text-gray-300 hover:text-white p-2 m-4 rounded-lg absolute top-15 left-0 z-10 flex items-center justify-center w-10 h-10">
        <i class="fas fa-chevron-right text-sm rotate-icon"></i>
    </button>

    <!-- Sidebar -->
    <div id="sidebar" class="w-full md:w-80 bg-[#1e1e1e] p-4 md:p-8 space-y-6 md:space-y-8 transform transition-transform duration-300 ease-in-out md:transform-none hidden md:block">
        <div class="flex flex-col items-center space-y-4">
            <div class="relative">
                <div class="h-24 w-24 md:h-32 md:w-32 bg-gradient-to-r from-red-600 to-red-800 rounded-full p-1">
                    <div class="h-full w-full bg-white rounded-full flex items-center justify-center overflow-hidden">
                        <img src="{{ Auth::user()->profile_image ? asset('storage/'.Auth::user()->profile_image) : asset('assets/default-profile.jpg') }}" alt="Profile" class="h-full w-full object-cover">
                    </div>
                </div>
                <div class="absolute bottom-2 right-2 h-4 w-4 bg-green-500 rounded-full border-2 border-white"></div>
            </div>
            <div class="text-center">
                <h2 class="text-xl md:text-2xl font-bold">{{ Auth::user()->full_name }}</h2>
                <p class="text-gray-400 text-xs md:text-sm">Member since {{ Auth::user()->created_at->format('m/d/Y') }}</p>
            </div>
        </div>

        <!-- QR Code -->
        <div class="bg-[#2d2d2d] hover-card p-4 md:p-6 rounded-xl">
            <h3 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-center">Check-In QR</h3>
            <div class="bg-white p-2 md:p-3 rounded-lg flex justify-center cursor-pointer" id="qrCodeContainer" onclick="openQrModal()">
                <div class="w-32 h-32 md:w-40 md:h-40">
                    {!! QrCode::size(150)->generate(Auth::user()->qr_code) !!}
                </div>
            </div>
            <div class="flex justify-center mt-2">
                <a href="{{ route('profile.qr') }}" class="text-xs text-center text-blue-400 hover:text-blue-300">View full screen</a>
            </div>
            <p class="text-xs text-center mt-2 md:mt-3 text-gray-400">Tap the QR code to enlarge</p>
        </div>

        <!-- Personal Information -->
        <div class="bg-[#2d2d2d] hover-card p-4 md:p-6 rounded-xl space-y-3 md:space-y-4">
            <h3 class="text-base md:text-lg font-semibold mb-2 md:mb-4">Personal Information</h3>
            <div class="space-y-3 md:space-y-4">
                <div class="flex justify-between items-center">
                    <label class="text-gray-400 text-sm">Gender</label>
                    <p class="font-medium text-sm md:text-base">{{ strtoupper(Auth::user()->gender) }}</p>
                </div>
                <div class="flex justify-between items-center">
                    <label class="text-gray-400 text-sm">Fitness Goals</label>
                    <p class="font-medium text-sm md:text-base">{{ strtoupper(str_replace('-', ' ', Auth::user()->fitness_goal)) }}</p>
                </div>
                <div class="flex justify-between items-center">
                    <label class="text-gray-400 text-sm">Mobile Number</label>
                    <p class="font-medium text-sm md:text-base">{{ Auth::user()->mobile_number ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="bg-[#2d2d2d] hover-card p-4 md:p-6 rounded-xl space-y-3 md:space-y-4">
            <h3 class="text-base md:text-lg font-semibold mb-2 md:mb-4">Quick Links</h3>
            <div class="space-y-3">
                <a href="{{ route('orders') }}" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200 flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-shopping-bag mr-2"></i> My Orders
                </a>
                <a href="{{ route('account.settings') }}" class="w-full bg-[#374151] text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-user-cog mr-2"></i> Account Settings
                </a>
                <a href="{{ route('payment-method') }}" class="w-full bg-[#374151] text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-credit-card mr-2"></i> Payment Methods
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-4 md:p-8 overflow-auto mt-16 md:mt-0">
        <!-- Attendance Graph -->
        <div class="bg-[#2d2d2d] hover-card rounded-xl shadow-lg p-4 md:p-6 mb-4 md:mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 space-y-3 sm:space-y-0">
                <h2 class="text-lg md:text-xl font-semibold">Attendance</h2>
                <div class="flex space-x-2 w-full sm:w-auto overflow-x-auto py-1 sm:py-0">
                    <button class="px-3 py-1 rounded-full bg-[#121212] text-xs md:text-sm whitespace-nowrap hover:bg-opacity-80 transition">1D</button>
                    <button class="px-3 py-1 rounded-full bg-[#121212] text-xs md:text-sm whitespace-nowrap hover:bg-opacity-80 transition">1W</button>
                    <button class="px-3 py-1 rounded-full bg-red-600 text-white text-xs md:text-sm whitespace-nowrap hover:bg-red-700 transition">1M</button>
                    <button class="px-3 py-1 rounded-full bg-[#121212] text-xs md:text-sm whitespace-nowrap hover:bg-opacity-80 transition">1Y</button>
                </div>
            </div>
            
            <!-- Fallback attendance data visualization (for mobile and if Chart.js fails) -->
            <div class="mb-4 overflow-x-auto">
                <div class="flex items-end h-32 md:h-48 mt-4 md:mt-8 min-w-[500px]">
                    <div class="w-1/12 h-1 bg-blue-500"></div>
                    <div class="w-1/12 h-3 bg-blue-500"></div>
                    <div class="w-1/12 h-24 md:h-32 bg-blue-500"></div>
                    <div class="w-1/12 h-28 md:h-40 bg-blue-500"></div>
                    <div class="w-1/12 h-1 bg-blue-500"></div>
                    <div class="w-1/12 h-1 bg-blue-500"></div>
                    <div class="w-1/12 h-1 bg-blue-500"></div>
                    <div class="w-1/12 h-1 bg-blue-500"></div>
                    <div class="w-1/12 h-1 bg-blue-500"></div>
                    <div class="w-1/12 h-1 bg-blue-500"></div>
                    <div class="w-1/12 h-2 bg-blue-500"></div>
                    <div class="w-1/12 h-2 bg-blue-500"></div>
                </div>
                <div class="flex text-xs text-gray-400 mt-2 min-w-[500px]">
                    <div class="w-1/12 text-center">Jan</div>
                    <div class="w-1/12 text-center">Feb</div>
                    <div class="w-1/12 text-center">Mar</div>
                    <div class="w-1/12 text-center">Apr</div>
                    <div class="w-1/12 text-center">May</div>
                    <div class="w-1/12 text-center">Jun</div>
                    <div class="w-1/12 text-center">Jul</div>
                    <div class="w-1/12 text-center">Aug</div>
                    <div class="w-1/12 text-center">Sep</div>
                    <div class="w-1/12 text-center">Oct</div>
                    <div class="w-1/12 text-center">Nov</div>
                    <div class="w-1/12 text-center">Dec</div>
                </div>
            </div>
            
            <!-- Hidden Chart.js canvas that will be shown if script works -->
            <div id="chartContainer" class="hidden" style="height: 200px; md:height: 300px; position: relative; width: 100%;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Membership Plan -->
        <div class="bg-[#2d2d2d] hover-card rounded-xl shadow-lg p-4 md:p-6 mb-4 md:mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 space-y-3 sm:space-y-0">
                <h2 class="text-lg md:text-xl font-semibold">Membership Plan</h2>
                <a href="{{ route('pricing.gym') }}" class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 text-sm w-full sm:w-auto">
                    @if (!$activeSubscription)
                        Add Plan
                    @else
                        Change Plan
                    @endif
                </a>
            </div>
            
            @if ($activeSubscription)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                <div class="space-y-3 md:space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Type</span>
                        <span class="font-medium text-sm md:text-base">{{ strtoupper($activeSubscription->type) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Status</span>
                        <span class="px-2 py-0.5 md:px-3 md:py-1 bg-green-500 bg-opacity-20 text-green-500 rounded-full text-xs md:text-sm font-medium">ACTIVE</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Plan</span>
                        <span class="font-medium text-sm md:text-base">{{ strtoupper($activeSubscription->plan) }}</span>
                    </div>
                </div>
                <div class="space-y-3 md:space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Start Date</span>
                        <span class="font-medium text-sm md:text-base">{{ $activeSubscription->start_date ? $activeSubscription->start_date->format('Y-m-d') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">End Date</span>
                        <span class="font-medium text-sm md:text-base">{{ $activeSubscription->end_date ? $activeSubscription->end_date->format('Y-m-d') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar (only for subscriptions with end dates) -->
            @if($activeSubscription->end_date)
                @php
                    $startDate = $activeSubscription->start_date->timestamp;
                    $endDate = $activeSubscription->end_date->timestamp;
                    $currentDate = time();
                    $totalDuration = $endDate - $startDate;
                    $elapsed = $currentDate - $startDate;
                    $percentComplete = min(100, max(0, ($elapsed / $totalDuration) * 100));
                @endphp
            <div class="mt-4 md:mt-6">
                <div class="flex justify-between text-xs md:text-sm mb-1">
                    <span>Membership Duration</span>
                    <span>{{ round($percentComplete) }}% Complete</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full" style="width: {{ $percentComplete }}%"></div>
                </div>
            </div>
            @endif
            
            <div class="mt-4 md:mt-6 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('pricing.gym') }}" class="flex-1 bg-[#374151] text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-sync-alt mr-2"></i> Change Plan
                </a>
                <a href="{{ route('subscription.history') }}" class="flex-1 bg-[#374151] text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-history mr-2"></i> View History
                </a>
                <form action="{{ route('subscription.cancel', $activeSubscription->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" onclick="return confirm('Are you sure you want to cancel your subscription?')" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200 text-sm md:text-base">
                        <i class="fas fa-ban mr-2"></i> Cancel Subscription
                    </button>
                </form>
            </div>
            @else
            <div class="py-6 text-center bg-[#1e1e1e] rounded-lg">
                <i class="fas fa-dumbbell text-gray-500 text-4xl mb-3"></i>
                <p class="text-gray-400 mb-4">You don't have any active membership plan.</p>
                <a href="{{ route('pricing.gym') }}" class="bg-red-600 text-white py-2 px-6 rounded-lg hover:bg-red-700 transition duration-200 inline-flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-plus mr-2"></i> Get Membership
                </a>
            </div>
            @endif
        </div>

        <!-- Payment History -->
        <div class="bg-[#2d2d2d] hover-card rounded-xl shadow-lg p-4 md:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 space-y-3 sm:space-y-0">
                <h2 class="text-lg md:text-xl font-semibold">Payment History</h2>
                <div class="flex gap-2 w-full sm:w-auto">
                    <select id="filterType" class="bg-[#374151] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] text-sm px-3 py-2">
                        <option value="">All Types</option>
                        <option value="product">Products</option>
                        <option value="subscription">Subscriptions</option>
                    </select>
                    <input 
                        type="date" 
                        id="dateFilter"
                        class="bg-[#374151] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] text-sm px-3 py-2"
                    >
                </div>
            </div>
            <div class="overflow-x-auto -mx-4 md:mx-0">
                <div class="min-w-[600px] px-4 md:px-0">
                    <table class="w-full" id="invoiceTable">
                        <thead>
                            <tr class="text-left border-b border-gray-700">
                                <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Invoice #</th>
                                <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Date</th>
                                <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Type</th>
                                <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Amount</th>
                                <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($invoices) && count($invoices) > 0)
                                @foreach($invoices as $invoice)
                                <tr class="border-b border-gray-700 hover:bg-[#374151]">
                                    <td class="py-3 md:py-4 text-xs md:text-sm">
                                        {{ substr($invoice->invoice_number ?? 'N/A', 0, 10) }}...
                                    </td>
                                    <td class="py-3 md:py-4 text-xs md:text-sm">
                                        {{ \Carbon\Carbon::parse($invoice->invoice_date ?? now())->format('Y-m-d') }}
                                    </td>
                                    <td class="py-3 md:py-4 text-xs md:text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs 
                                            {{ ($invoice->type ?? '') === 'subscription' ? 'bg-blue-900 text-blue-200' : 'bg-green-900 text-green-200' }}">
                                            {{ ucfirst($invoice->type ?? 'unknown') }}
                                        </span>
                                    </td>
                                    <td class="py-3 md:py-4 text-red-500 font-medium text-xs md:text-sm">
                                        ₱{{ number_format($invoice->total_amount ?? 0, 2) }}
                                    </td>
                                    <td class="py-3 md:py-4 flex space-x-2">
                                        <button class="text-gray-400 hover:text-white transition-colors" 
                                        onclick="openReceiptModal('{{ $invoice->invoice_number ?? 'Unknown' }}', {{ json_encode([
                                            'date' => \Carbon\Carbon::parse($invoice->invoice_date ?? now())->format('Y-m-d'),
                                            'type' => ucfirst($invoice->type ?? 'unknown'),
                                            'amount' => number_format($invoice->total_amount ?? 0, 2),
                                            'items' => collect($invoice->items ?? [])->map(function($item) {
                                                return [
                                                    'description' => $item['description'] ?? $item->description ?? 'Unknown item',
                                                    'amount' => number_format(isset($item['amount']) ? $item['amount'] : (isset($item->amount) ? $item->amount : 0), 2)
                                                ];
                                            })
                                        ]) }})">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        @if(isset($invoice->id))
                                        <a href="{{ route('user.invoices.receipt', $invoice->id) }}" class="text-gray-400 hover:text-white transition-colors">
                                            <i class="fas fa-print text-sm"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-400">
                                    No payment history found. Your purchases and subscriptions will appear here.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include QR Code Modal from partials -->
@include('profile.partials.qr-code-modal')

<!-- Include Receipt Modal from partials -->
@include('profile.partials.receipt-modal')

<!-- Inline script for immediate execution -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar toggle functionality for mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('hidden');
                
                // Add animation to the icon
                const icon = sidebarToggle.querySelector('i');
                if (icon) {
                    if (sidebar.classList.contains('hidden')) {
                        icon.classList.remove('fa-chevron-left');
                        icon.classList.add('fa-chevron-right');
                        icon.classList.remove('rotate-icon-open');
                    } else {
                        icon.classList.remove('fa-chevron-right');
                        icon.classList.add('fa-chevron-left');
                        icon.classList.add('rotate-icon-open');
                    }
                }
            });
        }
        
        // QR Code Modal
        window.openQrModal = function() {
            document.getElementById('qrModal').classList.add('modal-open');
        };
        
        window.closeQrModal = function() {
            document.getElementById('qrModal').classList.remove('modal-open');
        };
        
        // Receipt Modal
        window.openReceiptModal = function(invoiceNumber, data) {
            // Set receipt title
            document.getElementById('receipt-title').textContent = 'Receipt #' + invoiceNumber.substring(0, 8);
            
            // Set receipt details
            document.getElementById('receiptInvoiceNumber').textContent = invoiceNumber;
            document.getElementById('receiptDate').textContent = data.date;
            
            // Set receipt type with colored badge
            const typeElement = document.getElementById('receiptType');
            typeElement.innerHTML = '';
            const typeBadge = document.createElement('span');
            typeBadge.className = data.type.toLowerCase() === 'subscription' 
                ? 'px-2 py-1 bg-blue-800 text-blue-200 rounded-full text-xs font-medium' 
                : 'px-2 py-1 bg-green-800 text-green-200 rounded-full text-xs font-medium';
            typeBadge.textContent = data.type;
            typeElement.appendChild(typeBadge);
            
            // Set receipt amount
            document.getElementById('receiptAmount').textContent = '₱' + data.amount;
            
            // Set items
            const itemsContainer = document.getElementById('receiptItems');
            itemsContainer.innerHTML = '';
            
            data.items.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="py-2 px-3">${item.description}</td>
                    <td class="py-2 px-3 text-right">₱${item.amount}</td>
                `;
                itemsContainer.appendChild(row);
            });
            
            document.getElementById('receiptModal').classList.add('modal-open');
        };
        
        window.closeReceiptModal = function() {
            document.getElementById('receiptModal').classList.remove('modal-open');
        };
        
        window.printReceipt = function() {
            alert('Printing functionality will be implemented here');
        };
        
        // Close modals when clicking outside of them
        window.addEventListener('click', function(event) {
            const qrModal = document.getElementById('qrModal');
            const receiptModal = document.getElementById('receiptModal');
            
            if (event.target === qrModal) {
                closeQrModal();
            }
            
            if (event.target === receiptModal) {
                closeReceiptModal();
            }
        });
        
        // Filter invoice table
        const searchInput = document.getElementById('filterType');
        const dateFilter = document.getElementById('dateFilter');
        const rows = document.querySelectorAll('#invoiceTable tbody tr');
        
        const filterTable = () => {
            const typeFilter = searchInput.value.toLowerCase();
            const dateValue = dateFilter.value;
            
            rows.forEach(row => {
                if (row.cells.length < 3) return; // Skip "No payment history" row
                
                const type = row.cells[2].textContent.toLowerCase().trim();
                const date = row.cells[1].textContent.trim();
                
                let shouldShow = true;
                
                // Check type filter
                if (typeFilter && !type.includes(typeFilter)) {
                    shouldShow = false;
                }
                
                // Check date filter
                if (dateValue && date !== dateValue) {
                    shouldShow = false;
                }
                
                row.style.display = shouldShow ? '' : 'none';
            });
        };
        
        if (searchInput) searchInput.addEventListener('change', filterTable);
        if (dateFilter) dateFilter.addEventListener('input', filterTable);
        
        // Chart.js initialization
        try {
            // Get the canvas and container
            const chartContainer = document.getElementById('chartContainer');
            const canvas = document.getElementById('attendanceChart');
            const ctx = canvas.getContext('2d');
            
            // Create and render chart
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    datasets: [{
                        label: 'Gym Visits',
                        data: [0.1, 0.5, 4.0, 5.0, 0.2, 0.1, 0.1, 0.1, 0.1, 0.1, 0.2, 0.3],
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.2,
                        fill: true,
                        pointBackgroundColor: '#0d6efd',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5.5,
                            grid: {
                                color: 'rgba(200, 200, 200, 0.2)'
                            },
                            ticks: {
                                stepSize: 0.5,
                                color: '#9ca3af',
                                font: {
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(200, 200, 200, 0.2)'
                            },
                            ticks: {
                                color: '#9ca3af',
                                font: {
                                    size: 9
                                },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Attendance',
                            color: '#fff',
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            padding: {
                                top: 0,
                                bottom: 20
                            },
                            align: 'start'
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#000',
                            bodyColor: '#000',
                            padding: 12,
                            displayColors: false,
                            borderColor: '#ddd',
                            borderWidth: 1
                        }
                    }
                }
            });
            
            // Show the chart container and hide the fallback
            chartContainer.classList.remove('hidden');
            document.querySelector('.flex.items-end').parentNode.classList.add('hidden');
            
        } catch (error) {
            console.error('Error creating chart:', error);
            // Fallback is already visible
        }
    });
</script>

@endsection
