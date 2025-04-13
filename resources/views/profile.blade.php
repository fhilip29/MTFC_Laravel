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
</style>
@endsection

@section('content')
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
                        <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="Profile" class="h-20 w-20 md:h-28 md:w-28 object-cover">
                    </div>
                </div>
                <div class="absolute bottom-2 right-2 h-4 w-4 bg-green-500 rounded-full border-2 border-white"></div>
            </div>
            <div class="text-center">
                <h2 class="text-xl md:text-2xl font-bold">King Dranreb Languido</h2>
                <p class="text-gray-400 text-xs md:text-sm">Member since 03/29/2023</p>
            </div>
        </div>

        <!-- QR Code -->
        <div class="bg-[#2d2d2d] p-4 md:p-6 rounded-xl">
            <h3 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-center">Check-In QR</h3>
            <div class="bg-white p-2 md:p-3 rounded-lg flex justify-center">
                <div class="w-32 h-32 md:w-40 md:h-40">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=MTFC123456789" alt="QR Code" class="w-full h-full">
                </div>
            </div>
            <p class="text-xs text-center mt-2 md:mt-3 text-gray-400">Scan this QR code at the entrance</p>
        </div>

        <!-- Personal Information -->
        <div class="bg-[#2d2d2d] p-4 md:p-6 rounded-xl space-y-3 md:space-y-4">
            <h3 class="text-base md:text-lg font-semibold mb-2 md:mb-4">Personal Information</h3>
            <div class="space-y-3 md:space-y-4">
                <div class="flex justify-between items-center">
                    <label class="text-gray-400 text-sm">Gender</label>
                    <p class="font-medium text-sm md:text-base">MALE</p>
                </div>
                <div class="flex justify-between items-center">
                    <label class="text-gray-400 text-sm">Fitness Goals</label>
                    <p class="font-medium text-sm md:text-base">LOSE WEIGHT</p>
                </div>
                <div class="flex justify-between items-center">
                    <label class="text-gray-400 text-sm">Mobile Number</label>
                    <p class="font-medium text-sm md:text-base">09770772168</p>
                </div>
            </div>
        </div>
        
        <!-- Billing Details -->
        <div class="bg-[#2d2d2d] p-4 md:p-6 rounded-xl space-y-3 md:space-y-4">
            <h3 class="text-base md:text-lg font-semibold mb-2 md:mb-4">Billing Details</h3>
            <div class="space-y-3 md:space-y-4">
                <div class="flex items-center">
                    <div class="bg-gray-700 p-2 rounded mr-2 md:mr-3">
                        <i class="fas fa-credit-card text-gray-300 text-sm md:text-base"></i>
                    </div>
                    <div>
                        <p class="font-medium text-sm md:text-base">VISA •••• 4582</p>
                        <p class="text-xs text-gray-400">Expires 09/2025</p>
                    </div>
                    <div class="ml-auto">
                        <span class="px-2 py-0.5 md:py-1 bg-green-500 bg-opacity-20 text-green-500 rounded-full text-xs">Primary</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('payment-method') }}" class="mt-3 md:mt-4 w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200 flex items-center justify-center text-sm md:text-base">
                <i class="fas fa-plus mr-2"></i> Add Payment Method
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-4 md:p-8 overflow-auto mt-16 md:mt-0">
        <!-- Attendance Graph -->
        <div class="bg-[#2d2d2d] rounded-xl shadow-lg p-4 md:p-6 mb-4 md:mb-6">
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
        <div class="bg-[#2d2d2d] rounded-xl shadow-lg p-4 md:p-6 mb-4 md:mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 space-y-3 sm:space-y-0">
                <h2 class="text-lg md:text-xl font-semibold">Membership Plan</h2>
                <button class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 text-sm w-full sm:w-auto">
                    Add Plan
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                <div class="space-y-3 md:space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Type</span>
                        <span class="font-medium text-sm md:text-base">GYM</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Status</span>
                        <span class="px-2 py-0.5 md:px-3 md:py-1 bg-green-500 bg-opacity-20 text-green-500 rounded-full text-xs md:text-sm font-medium">ACTIVE</span>
                    </div>
                </div>
                <div class="space-y-3 md:space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Start Date</span>
                        <span class="font-medium text-sm md:text-base">2023-04-06</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">End Date</span>
                        <span class="font-medium text-sm md:text-base">2024-04-06</span>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4 md:mt-6">
                <div class="flex justify-between text-xs md:text-sm mb-1">
                    <span>Membership Duration</span>
                    <span>65% Complete</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full" style="width: 65%"></div>
                </div>
            </div>
            
            <button class="mt-4 md:mt-6 w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200 text-sm md:text-base">
                Renew Membership
            </button>
        </div>

        <!-- Payment History -->
        <div class="bg-[#2d2d2d] rounded-xl shadow-lg p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold mb-4 md:mb-6">Payment History</h2>
            <div class="overflow-x-auto -mx-4 md:mx-0">
                <div class="min-w-[600px] px-4 md:px-0">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-gray-700">
                                <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Date</th>
                                <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Amount</th>
                                <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Transaction</th>
                                <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Payment Method</th>
                                <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-700">
                                <td class="py-3 md:py-4 text-xs md:text-sm">2023-04-05</td>
                                <td class="py-3 md:py-4 text-red-500 font-medium text-xs md:text-sm">₱500</td>
                                <td class="py-3 md:py-4 text-xs md:text-sm">ORDER</td>
                                <td class="py-3 md:py-4 text-xs md:text-sm">GCASH</td>
                                <td class="py-3 md:py-4">
                                    <button class="text-gray-400 hover:text-white transition-colors">
                                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-700">
                                <td class="py-3 md:py-4 text-xs md:text-sm">2023-03-29</td>
                                <td class="py-3 md:py-4 text-red-500 font-medium text-xs md:text-sm">₱2,000</td>
                                <td class="py-3 md:py-4 text-xs md:text-sm">MEMBERSHIP</td>
                                <td class="py-3 md:py-4 text-xs md:text-sm">CREDIT CARD</td>
                                <td class="py-3 md:py-4">
                                    <button class="text-gray-400 hover:text-white transition-colors">
                                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        </div>
    </div>
</div>

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
