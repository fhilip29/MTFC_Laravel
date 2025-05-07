@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
    $stats = [
        ['label' => 'New Visitor', 'count' => 8, 'icon' => 'fas fa-user-plus', 'color' => 'bg-blue-500'],
        ['label' => 'New Members', 'count' => 4, 'icon' => 'fas fa-users', 'color' => 'bg-green-500'],
        ['label' => 'Cancelled Membership', 'count' => 0, 'icon' => 'fas fa-times-circle', 'color' => 'bg-yellow-500'],
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

<div class="container mx-auto px-4 py-8">
    {{-- Membership Stats --}}
    <h2 class="text-lg font-semibold text-white mb-4">Membership Stats</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($membershipStats ?? [
            ['label' => 'Active Members', 'count' => '127', 'icon' => 'fas fa-user-check', 'color' => 'bg-blue-500'],
            ['label' => 'New Signups (This Month)', 'count' => '24', 'icon' => 'fas fa-user-plus', 'color' => 'bg-green-500'],
            ['label' => 'Subscription Revenue (This Month)', 'count' => '₱45,000', 'icon' => 'fas fa-credit-card', 'color' => 'bg-indigo-500'],
            ['label' => 'Cancelled Membership', 'count' => '3', 'icon' => 'fas fa-times-circle', 'color' => 'bg-yellow-500']
        ] as $stat)
        <div class="bg-[#1F2937] p-5 rounded-2xl shadow-md flex items-center space-x-4 hover:shadow-lg transition border border-[#374151]">
            <div class="{{ $stat['color'] }} text-white p-3 rounded-full">
                <i class="{{ $stat['icon'] }}"></i>
            </div>
            <div>
                <div class="text-sm text-[#9CA3AF]">{{ $stat['label'] }}</div>
                <div class="text-2xl font-bold text-white">{{ $stat['count'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Product Stats --}}
    <h2 class="text-lg font-semibold text-white mb-4">Product Stats</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($productStats ?? [
            ['label' => 'Total Products Sold (This Month)', 'count' => '42', 'icon' => 'fas fa-shopping-cart', 'color' => 'bg-purple-500'],
            ['label' => 'Inventory Low Stock Items', 'count' => '5', 'icon' => 'fas fa-exclamation-triangle', 'color' => 'bg-red-500'],
            ['label' => 'Top Product', 'count' => 'Protein Shake', 'icon' => 'fas fa-trophy', 'color' => 'bg-amber-500'],
            ['label' => 'Product Sales (This Month)', 'count' => '₱18,500', 'icon' => 'fas fa-shopping-bag', 'color' => 'bg-pink-500']
        ] as $metric)
        <div class="bg-[#1F2937] p-5 rounded-2xl shadow-md flex items-center space-x-4 hover:shadow-lg transition border border-[#374151]">
            <div class="{{ $metric['color'] }} text-white p-3 rounded-full">
                <i class="{{ $metric['icon'] }}"></i>
            </div>
            <div>
                <div class="text-sm text-[#9CA3AF]">{{ $metric['label'] }}</div>
                <div class="text-2xl font-bold text-white">{{ $metric['count'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Product Sales Chart --}}
        <div class="bg-[#1F2937] p-6 rounded-2xl shadow-md border border-[#374151]">
            <h2 class="text-lg font-semibold mb-4 text-white">Product Sales</h2>
            <p class="text-sm text-[#9CA3AF] mb-4">Tracking one-time purchases like supplements, equipment, etc.</p>
            <canvas id="productChart" class="w-full h-64"></canvas>
        </div>

        {{-- Subscription Sales Chart --}}
        <div class="bg-[#1F2937] p-6 rounded-2xl shadow-md border border-[#374151]">
            <h2 class="text-lg font-semibold mb-4 text-white">Subscription Sales</h2>
            <p class="text-sm text-[#9CA3AF] mb-4">Tracking recurring revenue, member retention, plan types, etc.</p>
            <canvas id="subscriptionChart" class="w-full h-64"></canvas>
        </div>
    </div>

    {{-- Orders --}}
    <div class="bg-[#1F2937] p-6 rounded-2xl shadow-md border border-[#374151] mb-6">
        <h2 class="text-lg font-semibold mb-4 text-white">Latest Orders</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm table-auto">
                <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-xs">
                    <tr>
                        <th class="py-3 px-4 text-left">Reference No</th>
                        <th class="py-3 px-4 text-left">Order Date</th>
                        <th class="py-3 px-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="text-[#9CA3AF]">
                    @forelse($latestOrders ?? $orders as $order)
                    <tr class="hover:bg-[#374151] border-b border-[#374151]">
                        <td class="py-3 px-4">{{ $order instanceof \App\Models\Order ? $order->reference_no ?? $order->id : $order['id'] }}</td>
                        <td class="py-3 px-4">{{ $order instanceof \App\Models\Order ? $order->created_at->format('Y-m-d') : $order['date'] }}</td>
                        <td class="py-3 px-4">
                            <span class="text-white px-2 py-1 rounded text-xs {{ $order instanceof \App\Models\Order ? ($order->status === 'Completed' ? 'bg-green-500' : ($order->status === 'Cancelled' ? 'bg-red-500' : ($order->status === 'Pending' ? 'bg-gray-500' : 'bg-green-500'))) : $order['color'] }}">
                                {{ $order instanceof \App\Models\Order ? $order->status : $order['status'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-4 px-4 text-center text-[#9CA3AF]">No recent orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Client Messages -->
    <div class="bg-[#1F2937] p-6 rounded-2xl shadow-md border border-[#374151] mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-white">Client Messages</h2>
            <a href="{{ route('admin.messages') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg transition">
                View All
            </a>
        </div>
        
        <div class="client-messages overflow-auto" style="max-height: 380px;">
            @php
                // Get messages sent to admin (assuming admin id is 1)
                $adminId = \App\Models\User::where('role', 'admin')->first()->id ?? 1;
                $recentMessages = \App\Models\Message::with('sender')
                    ->where('recipient_id', $adminId)
                    ->whereNull('parent_id') // Only show parent messages (not replies)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            @endphp

            @if($recentMessages->count() > 0)
                <div class="space-y-4">
                    @foreach($recentMessages as $message)
                        <div class="flex space-x-3 p-3 {{ $message->is_read ? 'bg-[#2D3748]' : 'bg-[#374151]' }} rounded-lg">
                            <div class="flex-shrink-0">
                                @if($message->sender->profile_image)
                                    <img src="{{ asset($message->sender->profile_image) }}" alt="{{ $message->sender->full_name }}" class="h-10 w-10 rounded-full">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                        <span class="text-white font-semibold text-sm">{{ strtoupper(substr($message->sender->full_name, 0, 2)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between">
                                    <h3 class="text-sm font-semibold {{ $message->is_read ? 'text-white' : 'text-white' }}">
                                        {{ $message->sender->full_name }}
                                        <span class="ml-2 px-1.5 py-0.5 rounded-full text-xs 
                                            {{ $message->sender->role == 'admin' ? 'bg-red-500 text-white' : '' }}
                                            {{ $message->sender->role == 'trainer' ? 'bg-purple-500 text-white' : '' }}
                                            {{ $message->sender->role == 'member' ? 'bg-blue-500 text-white' : '' }}">
                                            {{ ucfirst($message->sender->role) }}
                                        </span>
                                        @if(!$message->is_read)
                                            <span class="ml-2 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">New</span>
                                        @endif
                                    </h3>
                                    <span class="text-xs text-[#9CA3AF]">{{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm font-medium text-white truncate">{{ $message->subject }}</p>
                                <p class="text-xs text-[#9CA3AF] mt-1 line-clamp-2">{{ Str::limit($message->content, 80) }}</p>
                                <a href="{{ route('admin.messages.show', $message->id) }}" class="inline-flex items-center mt-2 bg-blue-600 hover:bg-blue-700 text-white text-xs px-2 py-1 rounded transition">
                                    <i class="fas fa-reply mr-1"></i> Reply
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-8">
                    <i class="fas fa-envelope text-4xl text-[#4B5563] mb-3"></i>
                    <p class="text-[#9CA3AF]">No messages found</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Message Modal --}}
<div id="messageModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-[#1F2937] border border-[#374151] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-[101] relative">
            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-white mb-4" id="modal-title">Message Details</h3>
                        <div class="bg-[#111827] rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <h4 class="text-[#9CA3AF] text-xs uppercase mb-1">From</h4>
                                    <p class="text-white" id="modal-name"></p>
                                </div>
                                <div>
                                    <h4 class="text-[#9CA3AF] text-xs uppercase mb-1">Email</h4>
                                    <p class="text-white" id="modal-email"></p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h4 class="text-[#9CA3AF] text-xs uppercase mb-1">Subject</h4>
                                <p class="text-white font-medium" id="modal-subject"></p>
                            </div>
                            <div class="mb-4">
                                <h4 class="text-[#9CA3AF] text-xs uppercase mb-1">Message</h4>
                                <p class="text-white" id="modal-message"></p>
                            </div>
                            <div>
                                <h4 class="text-[#9CA3AF] text-xs uppercase mb-1">Received</h4>
                                <p class="text-white text-sm" id="modal-date"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-[#111827] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="markAsReadBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Mark as Read
                </button>
                <button type="button" id="replyBtn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Reply
                </button>
                <button type="button" id="closeModalBtn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-[#374151] text-base font-medium text-white hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Product Sales Chart
    const productCtx = document.getElementById('productChart').getContext('2d');
    const productChart = new Chart(productCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
            datasets: [{
                label: 'Product Sales',
                data: {!! json_encode($productSalesChartData ?? [0, 0, 0, 1400, 0, 0]) !!},
                borderColor: 'rgba(59, 130, 246, 1)',
                backgroundColor: 'rgba(59, 130, 246, 0.15)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(59, 130, 246, 1)',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#9CA3AF',
                        font: { size: 12 }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#9CA3AF' },
                    grid: { color: '#374151' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#9CA3AF' },
                    grid: { color: '#374151' }
                }
            }
        }
    });

    // Subscription Sales Chart
    const subscriptionCtx = document.getElementById('subscriptionChart').getContext('2d');
    const subscriptionChart = new Chart(subscriptionCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
            datasets: [{
                label: 'Subscription Sales',
                data: {!! json_encode($subscriptionSalesChartData ?? [0, 0, 0, 800, 0, 0]) !!},
                borderColor: 'rgba(16, 185, 129, 1)',
                backgroundColor: 'rgba(16, 185, 129, 0.15)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(16, 185, 129, 1)',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#9CA3AF',
                        font: { size: 12 }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#9CA3AF' },
                    grid: { color: '#374151' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#9CA3AF' },
                    grid: { color: '#374151' }
                }
            }
        }
    });

    // Message Modal Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const messageModal = document.getElementById('messageModal');
        const modalContent = messageModal.querySelector('.inline-block');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const markAsReadBtn = document.getElementById('markAsReadBtn');
        const replyBtn = document.getElementById('replyBtn');
        
        const modalName = document.getElementById('modal-name');
        const modalEmail = document.getElementById('modal-email');
        const modalSubject = document.getElementById('modal-subject');
        const modalMessage = document.getElementById('modal-message');
        const modalDate = document.getElementById('modal-date');
        
        // Get all view message buttons
        const viewBtns = document.querySelectorAll('.view-message-btn');
        
        // Add click event to all view buttons
        viewBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Get data from button attributes
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const subject = this.getAttribute('data-subject');
                const message = this.getAttribute('data-message');
                const date = this.getAttribute('data-date');
                
                // Set modal content
                modalName.textContent = name;
                modalEmail.textContent = email;
                modalSubject.textContent = subject;
                modalMessage.textContent = message;
                modalDate.textContent = date;
                
                // Store message ID for mark as read functionality
                markAsReadBtn.setAttribute('data-id', id);
                replyBtn.setAttribute('data-email', email);
                
                // Show modal
                messageModal.classList.remove('hidden');
                
                console.log('Modal opened for message ID:', id);
            });
        });
        
        // Close modal when clicking close button
        closeModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            messageModal.classList.add('hidden');
        });
        
        // Close modal when clicking outside the modal content
        messageModal.addEventListener('click', function(e) {
            if (e.target === messageModal) {
                messageModal.classList.add('hidden');
            }
        });
        
        // Prevent modal content click from closing the modal
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Mark as read functionality
        markAsReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            // Here you would typically make an AJAX request to mark the message as read
            
            // For demo purposes, just show SweetAlert
            Swal.fire({
                title: 'Success!',
                text: 'Message marked as read!',
                icon: 'success',
                confirmButtonColor: '#10B981'
            });
            messageModal.classList.add('hidden');
        });
        
        // Reply functionality
        replyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const email = this.getAttribute('data-email');
            // Here you would typically redirect to a compose message page or show a compose modal
            
            // For demo purposes, just show SweetAlert
            Swal.fire({
                title: 'Reply',
                text: 'Replying to: ' + email,
                icon: 'info',
                confirmButtonColor: '#3B82F6'
            });
        });
        
        // Show success message if present
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#10B981'
            });
        @endif
    });
</script>
@endsection
