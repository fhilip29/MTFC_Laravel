@extends('layouts.app')

@section('title', 'Trainer Profile')

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
    
    /* Chart container styles */
    #chartContainer {
        height: 200px;
        width: 100%;
        position: relative;
        margin-bottom: 1rem;
        background-color: #f9f9f9;
        border-radius: 0.5rem;
    }
    
    @media (min-width: 768px) {
        #chartContainer {
            height: 250px;
        }
    }
    
    /* Fallback chart styles */
    .fallback-chart .bar-container {
        height: 150px;
    }
    
    @media (min-width: 768px) {
        .fallback-chart .bar-container {
            height: 200px;
        }
    }
</style>
@endsection

@section('content')
<div class="bg-[#EDEDED] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-gray-800 to-black rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8 flex flex-col md:flex-row gap-8 items-center text-white relative">
                <!-- Profile Picture -->
                <div class="relative">
                    <div class="h-32 w-32 md:h-40 md:w-40 rounded-full border-4 border-white overflow-hidden bg-white">
                        <img src="{{ $trainer->profile_image_url ?? asset('assets/default_profile.png') }}" 
                            alt="{{ $trainer->user->full_name }}" 
                            class="w-full h-full object-cover">
                    </div>
                    <div class="absolute bottom-0 right-0">
                        <span class="h-6 w-6 bg-green-500 rounded-full border-2 border-white"></span>
                    </div>
                </div>
                
                <!-- Profile Info (Centered) -->
                <div class="flex-1 text-center">
                    <h1 class="text-3xl font-bold">{{ $trainer->user->full_name }}</h1>
                    <p class="text-gray-300 mt-1 text-lg">
                        <span class="font-semibold">Specialization:</span> {{ $trainer->specialization }}
                    </p>
                    <p class="text-gray-300 mt-1">
                        <span class="font-semibold">Email:</span> {{ $trainer->user->email }}
                    </p>
                    <p class="text-gray-300 mt-1">
                        <span class="font-semibold">Instructor for:</span> {{ $trainer->instructor_for }}
                    </p>
                    <div class="mt-3 max-h-32 overflow-y-auto px-4">
                        <p class="text-gray-100">{{ $trainer->short_intro }}</p>
                    </div>
                </div>
                
                <!-- QR Code and Edit Button (Right aligned) -->
                <div class="flex flex-col items-center space-y-4">
                    <!-- QR Code button instead of displaying it directly -->
                    <a href="{{ route('user.qr') }}" class="w-full px-6 py-3 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-qrcode mr-2"></i> View QR Code
                    </a>
                    
                    <!-- Edit Profile Button -->
                    <a href="{{ route('account.settings') }}" class="w-full px-6 py-3 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i> Edit Profile
                    </a>
                    
                    <!-- Messages Button -->
                    <a href="{{ route('user.messages') }}" class="w-full px-6 py-3 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-envelope mr-2"></i> Messages
                    </a>
                    
                    <!-- Attendance History Button -->
                    <a href="{{ route('trainer.attendance') }}" class="w-full px-6 py-3 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-calendar-check mr-2"></i> Attendance History
                    </a>
                </div>
            </div>
            
            <!-- Profile Stats -->
            <div class="flex flex-wrap justify-center gap-4 p-4 bg-gray-900 bg-opacity-50">
                <div class="bg-gray-100 p-4 rounded-xl text-center min-w-[140px] shadow-md">
                    <h3 class="text-3xl font-bold text-gray-800">{{ $activeMembers }}</h3>
                    <p class="text-sm text-gray-600 font-medium">Active Members</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-xl text-center min-w-[140px] shadow-md">
                    <h3 class="text-3xl font-bold text-gray-800">{{ count($upcomingSessions) }}</h3>
                    <p class="text-sm text-gray-600 font-medium">Upcoming Sessions</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-xl text-center min-w-[140px] shadow-md">
                    <h3 class="text-3xl font-bold text-gray-800">{{ $newStudents ?? 0 }}</h3>
                    <p class="text-sm text-gray-600 font-medium">New Students</p>
                </div>
            </div>
        </div>
        
        <!-- Attendance Chart -->
        <div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
            <div class="p-4 bg-gray-800 text-white border-b border-gray-700">
                <h2 class="text-xl font-bold">Attendance</h2>
                <p class="text-gray-300 text-sm">Your attendance history</p>
            </div>
            <div class="p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-3 sm:space-y-0">
                    <div class="flex space-x-2 w-full sm:w-auto overflow-x-auto py-1 sm:py-0">
                        <button class="attendance-period px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-xs md:text-sm whitespace-nowrap hover:bg-gray-300 transition" data-period="week">1W</button>
                        <button class="attendance-period px-3 py-1 rounded-full bg-red-600 text-white text-xs md:text-sm whitespace-nowrap hover:bg-red-700 transition active" data-period="month">1M</button>
                        <button class="attendance-period px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-xs md:text-sm whitespace-nowrap hover:bg-gray-300 transition" data-period="year">1Y</button>
                        <button class="attendance-period px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-xs md:text-sm whitespace-nowrap hover:bg-gray-300 transition" data-period="all">All</button>
                    </div>
                </div>
                
                <!-- Chart Container -->
                <div class="mb-4 overflow-x-auto fallback-chart">
                    <div class="flex items-end bar-container mt-4 min-w-[500px]">
                        <div class="w-1/12 h-1 bg-blue-500"></div>
                        <div class="w-1/12 h-3 bg-blue-500"></div>
                        <div class="w-1/12 h-12 bg-blue-500"></div>
                        <div class="w-1/12 h-16 bg-blue-500"></div>
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
                <div id="chartContainer" class="hidden" style="height: 250px; position: relative; width: 100%;">
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
                    <a href="{{ route('trainer.attendance') }}" class="inline-flex items-center text-red-600 hover:text-red-800 font-medium">
                        <span>View Complete Attendance History</span>
                        <i class="fas fa-chevron-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Content Grid -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Weekly Calendar -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="p-4 bg-gray-800 text-white border-b border-gray-700">
                    <h2 class="text-xl font-bold">Weekly Schedule</h2>
                    <p class="text-gray-300 text-sm">Your current training schedule</p>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                    <tr class="{{ isset($weeklySchedule[$day]) ? 'bg-gray-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ isset($weeklySchedule[$day]) ? 'text-gray-800' : 'text-gray-400' }}">
                                            {{ $day }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ isset($weeklySchedule[$day]) ? $weeklySchedule[$day]['start'] : 'No Session' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ isset($weeklySchedule[$day]) ? $weeklySchedule[$day]['end'] : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No schedule found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Upcoming Sessions -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="p-4 bg-gray-800 text-white border-b border-gray-700">
                    <h2 class="text-xl font-bold">Upcoming Sessions</h2>
                    <p class="text-gray-300 text-sm">Your next 5 training sessions</p>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($upcomingSessions as $session)
                        <div class="p-4 flex items-center gap-4">
                            <div class="bg-gray-200 text-gray-800 h-12 w-12 rounded-full flex items-center justify-center font-bold">
                                {{ substr($session['day'], 0, 3) }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">{{ $session['date'] }}</p>
                                <p class="font-semibold text-gray-800">{{ $session['start_time'] }} - {{ $session['end_time'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-sm text-gray-500">No upcoming sessions found</div>
                    @endforelse
                </div>
            </div>
            
            <!-- Active Members -->
            <div class="lg:col-span-3 bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="p-4 bg-gray-800 text-white border-b border-gray-700">
                    <h2 class="text-xl font-bold">Active Members</h2>
                    <p class="text-gray-300 text-sm">Members subscribed to your programs</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ends On</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($members as $member)
                                @foreach($member->subscriptions as $subscription)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200">
                                                    <img src="{{ $member->profile_image ? asset($member->profile_image) : asset('assets/default_profile.png') }}" 
                                                        alt="{{ $member->full_name }}" 
                                                        class="h-10 w-10 object-cover">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $member->full_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $subscription->type == 'gym' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $subscription->type == 'boxing' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $subscription->type == 'muay' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $subscription->type == 'jiu' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                {{ ucfirst($subscription->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ucfirst($subscription->plan) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $subscription->end_date ? date('M d, Y', strtotime($subscription->end_date)) : 'Ongoing' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <button onclick="viewMemberDetails('{{ $member->id }}', '{{ $member->full_name }}', '{{ $member->email }}', '{{ $subscription->type }}', '{{ $subscription->plan }}', '{{ $subscription->end_date ? date('M d, Y', strtotime($subscription->end_date)) : 'Ongoing' }}', '{{ $member->profile_image ? asset($member->profile_image) : asset('assets/default_profile.png') }}')" 
                                                class="text-gray-600 hover:text-red-600 transition-colors cursor-pointer">
                                                <i class="fas fa-eye mr-1"></i> See Details
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No active members found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Member Details Modal -->
<div id="memberDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-auto overflow-hidden">
        <div class="p-4 bg-gray-800 text-white flex justify-between items-center">
            <h3 class="text-lg font-bold" id="modalMemberName">Member Details</h3>
            <button onclick="closeMemberModal()" class="text-white hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center">
                    <div class="h-16 w-16 rounded-full bg-gray-200 mr-3 flex items-center justify-center overflow-hidden" id="modalMemberImage">
                        <img src="" alt="Profile Image" class="h-full w-full object-cover" id="modalMemberImageSrc">
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold" id="modalMemberFullName">Member Name</h4>
                        <p class="text-gray-500" id="modalMemberEmail">member@example.com</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 border-t border-gray-200 pt-4">
                    <div>
                        <p class="text-sm text-gray-500">Subscription Type</p>
                        <p class="font-medium" id="modalSubscriptionType">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Plan</p>
                        <p class="font-medium" id="modalSubscriptionPlan">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">End Date</p>
                        <p class="font-medium" id="modalSubscriptionEnd">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="font-medium text-green-600">Active</p>
                    </div>
                </div>
            </div>
            <div class="mt-6 text-center">
                <button onclick="closeMemberModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM loaded, initializing trainer attendance chart");
        
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
        
        // Initialize or update chart function
        function initOrUpdateChart() {
            try {
                console.log("Initializing/updating chart with data:", chartData);
                const canvas = document.getElementById('attendanceChart');
                if (!canvas) {
                    console.error("Canvas element 'attendanceChart' not found");
                    return;
                }
                
                const ctx = canvas.getContext('2d');
                if (!ctx) {
                    console.error("Could not get 2D context from canvas");
                    return;
                }
                
                if (attendanceChart) {
                    // Update existing chart
                    console.log("Updating existing chart");
                    attendanceChart.data = chartData;
                    attendanceChart.update();
                } else {
                    // Create new chart
                    console.log("Creating new chart");
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
                                        precision: 0,
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
                    console.log("Chart created successfully");
                }
                
                // Show the chart container and hide the fallback
                const chartContainer = document.getElementById('chartContainer');
                if (chartContainer) {
                    chartContainer.classList.remove('hidden');
                }
                
                const fallbackChart = document.querySelector('.fallback-chart');
                if (fallbackChart) {
                    fallbackChart.classList.add('hidden');
                }
                
            } catch (error) {
                console.error("Error creating/updating chart:", error);
            }
        }
        
        // Attach click handlers to period buttons
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
                
                // Get attendance data
                fetchAttendanceData(this.getAttribute('data-period'));
            });
        });
        
        // Function to fetch attendance data from the API
        function fetchAttendanceData(period = 'month') {
            console.log("Fetching attendance data for period:", period);
            
            // Set stats to loading state
            document.getElementById('thisMonthCount').textContent = '...';
            document.getElementById('lastMonthCount').textContent = '...';
            document.getElementById('totalDaysCount').textContent = '...';
            document.getElementById('avgDaysCount').textContent = '...';
            
            // Make API request
            fetch(`/api/trainer/attendance?period=${period}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('API request failed with status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received attendance data:', data);
                    
                    // Update chart data
                    chartData.labels = data.labels;
                    chartData.datasets[0].data = data.checkIns;
                    
                    // Update stats
                    document.getElementById('thisMonthCount').textContent = data.stats.thisMonth || 0;
                    document.getElementById('lastMonthCount').textContent = data.stats.lastMonth || 0;
                    document.getElementById('totalDaysCount').textContent = data.stats.totalDays || 0;
                    document.getElementById('avgDaysCount').textContent = data.stats.avgDaysPerMonth || 0;
                    
                    // Update chart
                    initOrUpdateChart();
                })
                .catch(error => {
                    console.error('Error fetching attendance data:', error);
                    document.getElementById('thisMonthCount').textContent = '-';
                    document.getElementById('lastMonthCount').textContent = '-';
                    document.getElementById('totalDaysCount').textContent = '-';
                    document.getElementById('avgDaysCount').textContent = '-';
                });
        }
        
        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded! Adding it dynamically...');
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
            script.onload = function() {
                console.log('Chart.js loaded dynamically');
                fetchAttendanceData('month');
            };
            document.head.appendChild(script);
        } else {
            console.log('Chart.js is already loaded');
            // Fetch initial data
            fetchAttendanceData('month');
        }

        // Function to view member details in modal
        function viewMemberDetails(id, name, email, type, plan, endDate, profileImageUrl) {
            document.getElementById('modalMemberFullName').textContent = name;
            document.getElementById('modalMemberEmail').textContent = email;
            document.getElementById('modalSubscriptionType').textContent = type.charAt(0).toUpperCase() + type.slice(1);
            document.getElementById('modalSubscriptionPlan').textContent = plan.charAt(0).toUpperCase() + plan.slice(1);
            document.getElementById('modalSubscriptionEnd').textContent = endDate;
            document.getElementById('modalMemberName').textContent = name + ' Details';
            document.getElementById('modalMemberImageSrc').src = profileImageUrl;
            
            // Show the modal
            document.getElementById('memberDetailsModal').classList.remove('hidden');
        }
        
        // Function to close the modal
        function closeMemberModal() {
            document.getElementById('memberDetailsModal').classList.add('hidden');
        }
        
        // Make functions globally available
        window.viewMemberDetails = viewMemberDetails;
        window.closeMemberModal = closeMemberModal;
    });
</script>
@endsection 