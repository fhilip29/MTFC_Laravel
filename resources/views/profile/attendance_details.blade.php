@extends('layouts.app')

@section('title', 'Attendance History')

@section('content')
<div class="bg-gray-100 min-h-screen py-6 md:py-10">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('profile') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-red-600">
                            <i class="fas fa-user mr-2"></i>
                            Profile
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs mx-1"></i>
                            <span class="text-sm font-medium text-gray-500">Attendance History</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Header and Stats -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Attendance History</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <h3 class="text-gray-500 text-sm font-medium mb-1">This Month</h3>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['thisMonth'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">check-ins</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <h3 class="text-gray-500 text-sm font-medium mb-1">Last Month</h3>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['lastMonth'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">check-ins</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <h3 class="text-gray-500 text-sm font-medium mb-1">Total Days</h3>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['totalDays'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">unique days</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <h3 class="text-gray-500 text-sm font-medium mb-1">Avg Days/Month</h3>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['avgDaysPerMonth'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">attendance consistency</p>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow p-4 mb-6 border border-gray-200">
            <h2 class="text-lg font-medium text-gray-800 mb-4">Filter Records</h2>
            
            <form action="{{ route('user.attendance') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Month Select -->
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <select id="month" name="month" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}" {{ $filter['month'] == $month ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Year Select -->
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select id="year" name="year" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Years</option>
                        @foreach($yearOptions as $year)
                            <option value="{{ $year }}" {{ $filter['year'] == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Status Select -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">All</option>
                        <option value="IN" {{ $filter['status'] == 'IN' ? 'selected' : '' }}>Check In</option>
                        <option value="OUT" {{ $filter['status'] == 'OUT' ? 'selected' : '' }}>Check Out</option>
                    </select>
                </div>
                
                <!-- Specific Date -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Specific Date</label>
                    <input type="date" id="date" name="date" value="{{ $filter['date'] }}" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 flex-1">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                    <a href="{{ route('user.attendance') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 flex-1 text-center">
                        <i class="fas fa-undo-alt mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Results Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Attendance Records</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Your gym attendance history listed by date and time.</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day of Week</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ Carbon\Carbon::parse($session->time)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ Carbon\Carbon::parse($session->time)->format('h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($session->status === 'IN')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-sign-in-alt mr-1"></i> Check In
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-sign-out-alt mr-1"></i> Check Out
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ Carbon\Carbon::parse($session->time)->format('l') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                                    No attendance records found. 
                                    @if(!empty($filter['date']) || !empty($filter['status']) || !empty($filter['month']) || !empty($filter['year']))
                                        Try adjusting your filters.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                {{ $sessions->withQueryString()->links() }}
            </div>
        </div>
        
        <!-- Summary Info -->
        <div class="mt-6">
            <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Tips for Tracking Attendance</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">About Check-ins</h4>
                            <p class="text-sm text-gray-500">
                                Always use your QR code to record both your entry and exit from the gym for accurate tracking.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-trophy text-yellow-500 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">Consistency is Key</h4>
                            <p class="text-sm text-gray-500">
                                Regular attendance helps you achieve your fitness goals faster. Aim for at least 3-4 visits per week.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 