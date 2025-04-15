@extends('layouts.admin')

@section('title', 'Session Management')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-700 flex items-center gap-2">
            <i class="fas fa-calendar-alt text-blue-500"></i> Session Management
        </h1>
        <div class="flex gap-2">
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md shadow-md flex items-center gap-2">
                <i class="fas fa-qrcode"></i> Scan
            </button>
            <button class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md shadow-md flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Guest
            </button>
        </div>
    </div>

    <input 
        type="text" 
        id="searchInput" 
        placeholder="Search by client ID or name..." 
        class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-300 shadow-sm"
    >
</div>

<div class="overflow-x-auto bg-white rounded-lg shadow-md">
    <table class="min-w-full divide-y divide-gray-200 text-sm text-left" id="sessionTable">
        <thead class="bg-gray-100 text-gray-600 uppercase sticky top-0 z-10">
            <tr>
                <th class="px-4 py-3">Client ID</th>
                <th class="px-4 py-3">Full Name</th>
                <th class="px-4 py-3">Time</th>
                <th class="px-4 py-3">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @php
                $sessions = [
                    ['id' => '67dfbb6ea96c19be54fae25b', 'name' => 'King Dranreb Languido', 'time' => '2025-04-05T15:30:15.000Z', 'status' => 'OUT'],
                    ['id' => '67dfbb6ea96c19be54fae25b', 'name' => 'King Dranreb Languido', 'time' => '2025-04-05T15:29:58.195Z', 'status' => 'IN'],
                    ['id' => '67dfbb6ea96c19be54fae25b', 'name' => 'King Dranreb Languido', 'time' => '2025-04-04T06:11:14.000Z', 'status' => 'OUT'],
                    ['id' => '67ed927ddd2e9713ad201512', 'name' => 'Tester Tester', 'time' => '2025-04-02T20:00:35.000Z', 'status' => 'OUT'],
                    ['id' => '67ed927ddd2e9713ad201512', 'name' => 'Tester Tester', 'time' => '2025-04-02T19:59:51.889Z', 'status' => 'IN'],
                    ['id' => '67dfbb6ea96c19be54fae25b', 'name' => 'King Dranreb Languido', 'time' => '2025-03-31T15:24:06.000Z', 'status' => 'OUT'],
                ];
            @endphp

            @foreach ($sessions as $session)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-gray-800">{{ $session['id'] }}</td>
                    <td class="px-4 py-3 font-medium">{{ $session['name'] }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($session['time'])->format('M d, Y h:i A') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $session['status'] === 'IN' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $session['status'] }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    // Simple search filter
    document.getElementById('searchInput').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#sessionTable tbody tr');

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>
@endsection
