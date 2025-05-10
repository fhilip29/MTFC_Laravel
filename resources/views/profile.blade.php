@extends('layouts.app')

@section('title', 'User Profile')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        background-color: rgba(0,0,0,0.5);
        transition: all 0.3s ease;
    }
    
    .modal-content {
        background-color: #ffffff;
        margin: 10% auto;
        padding: 25px;
        border-radius: 12px;
        max-width: 400px;
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
@php
    // Default value to prevent undefined variable error
    $activeSubscription = $activeSubscription ?? null;
    $invoices = $invoices ?? collect();
@endphp

<div class="flex flex-col md:flex-row min-h-screen bg-[#EDEDED] text-gray-800">
    <!-- Mobile Toggle for Sidebar - Moved lower -->
    <button id="sidebarToggle" class="md:hidden bg-white text-gray-700 hover:text-gray-900 p-2 m-4 rounded-lg absolute top-15 left-0 z-10 flex items-center justify-center w-10 h-10 shadow-md">
        <i class="fas fa-chevron-right text-sm rotate-icon"></i>
    </button>

    <!-- Sidebar -->
    <div id="sidebar" class="w-full md:w-80 bg-white p-4 md:p-8 space-y-6 md:space-y-8 transform transition-transform duration-300 ease-in-out md:transform-none hidden md:block shadow-lg">
        <div class="flex flex-col items-center space-y-4">
            <div class="relative">
                <div class="h-24 w-24 md:h-32 md:w-32 bg-gradient-to-r from-red-600 to-red-800 rounded-full p-1">
                    <div class="h-full w-full bg-white rounded-full flex items-center justify-center overflow-hidden">
                        <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/default-profile.jpg') }}" alt="Profile" class="h-full w-full object-cover">
                    </div>
                </div>
                <div class="absolute bottom-2 right-2 h-4 w-4 bg-green-500 rounded-full border-2 border-white"></div>
            </div>
            <div class="text-center">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">{{ Auth::user()->full_name }}</h2>
                <p class="text-gray-500 text-xs md:text-sm">Member since {{ Auth::user()->created_at->format('m/d/Y') }}</p>
            </div>
        </div>

        <!-- QR Code -->
        <div class="bg-white hover-card p-4 md:p-6 rounded-xl shadow-md border border-gray-200">
            <h3 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-center text-gray-800">Check-In QR</h3>
            <div class="bg-white p-2 md:p-3 rounded-lg flex justify-center cursor-pointer border border-gray-200" id="qrCodeContainer" onclick="openQrModal()">
                <div class="w-32 h-32 md:w-40 md:h-40">
                    {!! QrCode::size(150)->generate(Auth::user()->qr_code) !!}
                </div>
            </div>
            <div class="flex justify-center mt-2">
                <a href="{{ route('profile.qr') }}" class="text-xs text-center text-red-600 hover:text-red-800">View full screen</a>
            </div>
            <p class="text-xs text-center mt-2 md:mt-3 text-gray-500">Tap the QR code to enlarge</p>
        </div>

        <!-- Personal Information -->
        <div class="bg-white hover-card p-4 md:p-6 rounded-xl shadow-md border border-gray-200 space-y-3 md:space-y-4">
            <h3 class="text-base md:text-lg font-semibold mb-2 md:mb-4 text-gray-800">Personal Information</h3>
            <div class="space-y-3 md:space-y-4">
                <div class="flex justify-between items-center">
                    <label class="text-gray-500 text-sm">Gender</label>
                    <p class="font-medium text-sm md:text-base text-gray-800">{{ strtoupper(Auth::user()->gender) }}</p>
                </div>
                <div class="flex justify-between items-center">
                    <label class="text-gray-500 text-sm">Fitness Goals</label>
                    <p class="font-medium text-sm md:text-base text-gray-800">{{ strtoupper(str_replace('-', ' ', Auth::user()->fitness_goal)) }}</p>
                </div>
                <div class="flex justify-between items-center">
                    <label class="text-gray-500 text-sm">Mobile Number</label>
                    <p class="font-medium text-sm md:text-base text-gray-800">{{ Auth::user()->mobile_number ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="bg-white hover-card p-4 md:p-6 rounded-xl shadow-md border border-gray-200 space-y-3 md:space-y-4">
            <h3 class="text-base md:text-lg font-semibold mb-2 md:mb-4 text-gray-800">Quick Links</h3>
            <div class="space-y-3">
                <a href="{{ route('orders') }}" class="w-full bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-200 flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-shopping-bag mr-2"></i> My Orders
                </a>
                <a href="{{ route('account.settings') }}" class="w-full bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-200 flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-user-cog mr-2"></i> Account Settings
                </a>
                <a href="{{ route('subscription.history') }}" class="w-full bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-200 flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-history mr-2"></i> Subscription History
                </a>
                <a href="{{ route('user.messages') }}" class="w-full bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-200 flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-envelope mr-2"></i> Messages
                    @php
                        $unreadCount = Auth::user()->receivedMessages()->where('is_read', false)->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="ml-2 bg-red-600 text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-4 md:p-8 overflow-auto mt-16 md:mt-0">
        <!-- Attendance Graph -->
        <div class="bg-white hover-card rounded-xl shadow-lg p-4 md:p-6 mb-4 md:mb-6 border border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 space-y-3 sm:space-y-0">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800">Attendance</h2>
                <div class="flex space-x-2 w-full sm:w-auto overflow-x-auto py-1 sm:py-0">
                    <button class="attendance-period px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-xs md:text-sm whitespace-nowrap hover:bg-gray-300 transition" data-period="week">1W</button>
                    <button class="attendance-period px-3 py-1 rounded-full bg-red-600 text-white text-xs md:text-sm whitespace-nowrap hover:bg-red-700 transition active" data-period="month">1M</button>
                    <button class="attendance-period px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-xs md:text-sm whitespace-nowrap hover:bg-gray-300 transition" data-period="year">1Y</button>
                    <button class="attendance-period px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-xs md:text-sm whitespace-nowrap hover:bg-gray-300 transition" data-period="all">All</button>
                </div>
            </div>
            
            <!-- Fallback attendance data visualization (for mobile and if Chart.js fails) -->
            <div class="mb-4 overflow-x-auto fallback-chart">
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
                <div class="flex text-xs text-gray-500 mt-2 min-w-[500px]">
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
            
            <!-- Attendance Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 attendance-stats">
                <div class="bg-gray-100 p-3 rounded-lg text-center">
                    <p class="text-gray-500 text-xs mb-1">This Month</p>
                    <p class="text-gray-800 font-bold text-lg" id="thisMonthCount">-</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-lg text-center">
                    <p class="text-gray-500 text-xs mb-1">Last Month</p>
                    <p class="text-gray-800 font-bold text-lg" id="lastMonthCount">-</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-lg text-center">
                    <p class="text-gray-500 text-xs mb-1">Total Days</p>
                    <p class="text-gray-800 font-bold text-lg" id="totalDaysCount">-</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-lg text-center">
                    <p class="text-gray-500 text-xs mb-1">Avg Days/Month</p>
                    <p class="text-gray-800 font-bold text-lg" id="avgDaysCount">-</p>
                </div>
            </div>
            
            <!-- View All Attendance Link -->
            <div class="text-center mt-4">
                <a href="{{ route('user.attendance') }}" class="inline-flex items-center text-red-600 hover:text-red-800 font-medium">
                    <span>View Complete Attendance History</span>
                    <i class="fas fa-chevron-right ml-1 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Membership Plan -->
        <div class="bg-white hover-card rounded-xl shadow-lg p-4 md:p-6 mb-4 md:mb-6 border border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 space-y-3 sm:space-y-0">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800">Membership Plan</h2>
                <a href="{{ route('pricing.gym') }}" class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 text-sm w-full sm:w-auto">
                    @if (!$activeSubscription)
                        Add Plan
                    @else
                        View Plans
                    @endif
                </a>
            </div>
            
            @if ($activeSubscription)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                <div class="space-y-3 md:space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">Type</span>
                        <span class="font-medium text-sm md:text-base text-gray-800">{{ strtoupper($activeSubscription->type) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">Status</span>
                        <span class="px-2 py-0.5 md:px-3 md:py-1 bg-green-500 bg-opacity-20 text-green-600 rounded-full text-xs md:text-sm font-medium">ACTIVE</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">Plan</span>
                        <span class="font-medium text-sm md:text-base text-gray-800">{{ strtoupper($activeSubscription->plan) }}</span>
                    </div>
                </div>
                <div class="space-y-3 md:space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">Start Date</span>
                        <span class="font-medium text-sm md:text-base text-gray-800">{{ $activeSubscription->start_date ? $activeSubscription->start_date->format('Y-m-d') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">End Date</span>
                        <span class="font-medium text-sm md:text-base text-gray-800">{{ $activeSubscription->end_date ? $activeSubscription->end_date->format('Y-m-d') : 'N/A' }}</span>
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
                <div class="flex justify-between text-xs md:text-sm mb-1 text-gray-600">
                    <span>Membership Duration</span>
                    <span>{{ round($percentComplete) }}% Complete</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full" style="width: {{ $percentComplete }}%"></div>
                </div>
            </div>
            @endif
            
            <div class="mt-4 md:mt-6 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('subscription.history') }}" class="flex-1 bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-200 flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-history mr-2"></i> View Subscriptions
                </a>
                <form action="{{ route('subscription.cancel', $activeSubscription->id) }}" method="POST" class="flex-1" id="cancelSubscriptionForm">
                    @csrf
                    <button type="button" onclick="confirmCancelSubscription()" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200 text-sm md:text-base">
                        <i class="fas fa-ban mr-2"></i> Cancel Subscription
                    </button>
                </form>
            </div>
            @else
            <div class="py-6 text-center bg-gray-100 rounded-lg">
                <i class="fas fa-dumbbell text-gray-500 text-4xl mb-3"></i>
                <p class="text-gray-600 mb-4">You don't have any active membership plan.</p>
                <a href="{{ route('pricing.gym') }}" class="bg-red-600 text-white py-2 px-6 rounded-lg hover:bg-red-700 transition duration-200 inline-flex items-center justify-center text-sm md:text-base">
                    <i class="fas fa-plus mr-2"></i> Get Membership
                </a>
            </div>
            @endif
        </div>

        <!-- Payment History -->
        <div class="bg-white hover-card rounded-xl shadow-lg p-4 md:p-6 border border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 space-y-3 sm:space-y-0">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800">Payment History</h2>
                <div class="flex gap-2 w-full sm:w-auto">
                    <select id="filterType" class="bg-white border border-gray-300 text-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 text-sm px-3 py-2">
                        <option value="">All Types</option>
                        <option value="product">Products</option>
                        <option value="subscription">Subscriptions</option>
                    </select>
                    <input 
                        type="date" 
                        id="dateFilter"
                        class="bg-white border border-gray-300 text-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 text-sm px-3 py-2"
                    >
                </div>
            </div>
            
            <div class="overflow-x-auto rounded-lg shadow-sm -mx-4 md:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm text-left" id="invoiceTable">
                        <thead class="bg-gray-100 text-gray-500 uppercase text-xs sticky top-0 z-10">
                            <tr>
                                <th class="px-3 sm:px-4 py-3">Receipt Number</th>
                                <th class="px-3 sm:px-4 py-3">Type</th>
                                <th class="px-3 sm:px-4 py-3">Amount</th>
                                <th class="px-3 sm:px-4 py-3">Date</th>
                                <th class="px-3 sm:px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @if(count($invoices) > 0)
                                @foreach($invoices as $invoice)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 sm:px-4 py-3 font-mono text-gray-800 text-xs sm:text-sm">
                                        {{ Str::limit($invoice->invoice_number, 15) }}
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $invoice->type === 'subscription' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($invoice->type) }}
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 text-red-600 font-medium text-xs sm:text-sm">
                                        ₱{{ number_format($invoice->total_amount, 2) }}
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 text-gray-600 text-xs sm:text-sm">
                                        {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('user.payment.details', $invoice->id) }}" 
                                               class="text-gray-600 hover:text-gray-900 transition-colors"
                                               title="View Details">
                                                <i class="fas fa-eye text-sm"></i>
                                            </a>
                                            @if(isset($invoice->id))
                                            <a href="{{ route('user.payments.receipt', $invoice->id) }}" 
                                               class="text-gray-600 hover:text-gray-900 transition-colors" 
                                               target="_blank"
                                               title="Download Receipt">
                                                <i class="fas fa-download text-sm"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="px-3 sm:px-4 py-6 text-center text-gray-500">
                                        No payment history found. Your purchases and subscriptions will appear here.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if(count($invoices) > 0)
            <div class="flex justify-center mt-4">
                <a href="{{ route('user.payments') }}" class="bg-gray-800 text-white py-2 px-6 rounded-md hover:bg-gray-700 transition flex items-center gap-2 text-sm">
                    <i class="fas fa-history"></i> View All Payments
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Include QR Code Modal from partials -->
@include('profile.partials.qr-code-modal')

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
        
        // Filter invoice table
        const searchInput = document.getElementById('filterType');
        const dateFilter = document.getElementById('dateFilter');
        const rows = document.querySelectorAll('#invoiceTable tbody tr');
        
        const filterTable = () => {
            const typeFilter = searchInput.value.toLowerCase();
            const dateValue = dateFilter.value;
            
            rows.forEach(row => {
                if (row.cells.length < 3) return; // Skip "No payment history" row
                
                const type = row.cells[1].textContent.toLowerCase().trim();
                const date = row.cells[3].textContent.trim();
                
                let shouldShow = true;
                
                // Check type filter
                if (typeFilter && !type.includes(typeFilter)) {
                    shouldShow = false;
                }
                
                // Check date filter
                if (dateValue) {
                    const rowDate = new Date(date);
                    const filterDate = new Date(dateValue);
                    
                    if (rowDate.toDateString() !== filterDate.toDateString()) {
                        shouldShow = false;
                    }
                }
                
                row.style.display = shouldShow ? '' : 'none';
            });
        };
        
        if (searchInput) searchInput.addEventListener('change', filterTable);
        if (dateFilter) dateFilter.addEventListener('input', filterTable);
        
        // Attendance period buttons
        const periodButtons = document.querySelectorAll('.attendance-period');
        periodButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                periodButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-red-600', 'text-white');
                    btn.classList.add('bg-gray-200', 'text-gray-700');
                });
                
                // Add active class to clicked button
                this.classList.remove('bg-gray-200', 'text-gray-700');
                this.classList.add('active', 'bg-red-600', 'text-white');
                
                // Fetch attendance data for the selected period
                fetchAttendanceData(this.getAttribute('data-period'));
            });
        });
        
        // Chart.js variables
        let attendanceChart = null;
        let chartData = {
            labels: [],
            datasets: [{
                label: 'Gym Visits',
                data: [],
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
        };
        
        // Function to fetch attendance data
        function fetchAttendanceData(period = 'month') {
            // Show loading state
            document.getElementById('thisMonthCount').textContent = '...';
            document.getElementById('lastMonthCount').textContent = '...';
            document.getElementById('totalDaysCount').textContent = '...';
            document.getElementById('avgDaysCount').textContent = '...';
            
            fetch(`/api/user/attendance?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    // Update chart data
                    chartData.labels = data.labels;
                    chartData.datasets[0].data = data.checkIns;
                    
                    // Update stats
                    document.getElementById('thisMonthCount').textContent = data.stats.thisMonth || 0;
                    document.getElementById('lastMonthCount').textContent = data.stats.lastMonth || 0;
                    document.getElementById('totalDaysCount').textContent = data.stats.totalDays || 0;
                    document.getElementById('avgDaysCount').textContent = data.stats.avgDaysPerMonth || 0;
                    
                    // Init or update chart
                    initOrUpdateChart();
                })
                .catch(error => {
                    console.error('Error fetching attendance data:', error);
                    // Show error state
                    document.getElementById('thisMonthCount').textContent = '-';
                    document.getElementById('lastMonthCount').textContent = '-';
                    document.getElementById('totalDaysCount').textContent = '-';
                    document.getElementById('avgDaysCount').textContent = '-';
                });
        }
        
        // Function to initialize or update the chart
        function initOrUpdateChart() {
            try {
                const chartContainer = document.getElementById('chartContainer');
                const canvas = document.getElementById('attendanceChart');
                const ctx = canvas.getContext('2d');
                
                if (attendanceChart) {
                    // Update existing chart
                    attendanceChart.data = chartData;
                    attendanceChart.update();
                } else {
                    // Create new chart
                    attendanceChart = new Chart(ctx, {
                        type: 'line',
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)'
                                    },
                                    ticks: {
                                        stepSize: 1,
                                        color: '#6b7280',
                                        font: {
                                            size: 10
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)'
                                    },
                                    ticks: {
                                        color: '#6b7280',
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
                                    color: '#1f2937',
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
                }
                
                // Show the chart container and hide the fallback
                chartContainer.classList.remove('hidden');
                document.querySelector('.fallback-chart').classList.add('hidden');
                
            } catch (error) {
                console.error('Error creating chart:', error);
                // Fallback is already visible
            }
        }
        
        // Fetch initial attendance data
        fetchAttendanceData('month');
        
        // Check for unread messages periodically
        function checkUnreadMessages() {
            fetch('/api/user/unread-messages-count')
                .then(response => response.json())
                .then(data => {
                    const messageLink = document.querySelector('a[href="{{ route("user.messages") }}"]');
                    if (messageLink) {
                        // Remove existing badge if any
                        const existingBadge = messageLink.querySelector('.ml-2.bg-red-600');
                        if (existingBadge) {
                            existingBadge.remove();
                        }
                        
                        // Add new badge if there are unread messages
                        if (data.count > 0) {
                            const badge = document.createElement('span');
                            badge.className = 'ml-2 bg-red-600 text-white text-xs px-2 py-0.5 rounded-full';
                            badge.textContent = data.count;
                            messageLink.appendChild(badge);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking unread messages:', error);
                });
        }
        
        // Check initially and then every minute
        checkUnreadMessages();
        setInterval(checkUnreadMessages, 60000);
        
        // SweetAlert subscription cancellation confirmation
        window.confirmCancelSubscription = function() {
            Swal.fire({
                title: 'Cancel Subscription?',
                text: 'Are you sure you want to cancel your subscription? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('cancelSubscriptionForm').submit();
                }
            });
        };
    });
</script>

@endsection


