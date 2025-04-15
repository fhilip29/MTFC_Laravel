@extends('layouts.admin')

@section('title', 'Promotion Management')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-700 flex items-center gap-2">
            <i class="fas fa-bullhorn text-yellow-500"></i> Promotion Management
        </h1>
        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md shadow flex items-center gap-2">
            <i class="fas fa-plus"></i> Add Promotion
        </button>
    </div>

    <div class="relative mb-4">
        <input
            type="text"
            id="promotionSearch"
            placeholder="Search by title, description or target..."
            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
        >
        <i class="fas fa-search absolute top-3 left-3 text-gray-400"></i>
    </div>
</div>

<div class="overflow-x-auto bg-white rounded-lg shadow-md">
    <table class="min-w-full divide-y divide-gray-200 text-sm text-left" id="promotionTable">
        <thead class="bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Description</th>
                <th class="px-4 py-3">Target</th>
                <th class="px-4 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @php
                $promotions = [
                    ['title' => 'Muay Thai', 'description' => 'aslk|dbajhndajNj nn jajndkjandkjna...', 'target' => 'All Members'],
                    ['title' => 'Boxing', 'description' => 'Boxing Tournament', 'target' => 'All Members'],
                    ['title' => 'Sontukan', 'description' => 'sa manila', 'target' => 'Gym Members'],
                    ['title' => 'Tournament', 'description' => 'Tournament', 'target' => 'All Members'],
                    ['title' => 'Test', 'description' => 'test', 'target' => 'Taekwondo Members'],
                    ['title' => '100% Sale!', 'description' => 'test', 'target' => 'Boxing'],
                    ['title' => 'Edited promotion 4', 'description' => 'Taho sa bagong ilog', 'target' => 'boxing'],
                ];
            @endphp

            @foreach ($promotions as $promo)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $promo['title'] }}</td>
                    <td class="px-4 py-3 text-gray-700 max-w-xs truncate" title="{{ $promo['description'] }}">
                        {{ Str::limit($promo['description'], 80, '...') }}
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $promo['target'] }}</td>
                    <td class="px-4 py-3 text-center">
                        <button class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    // Simple search filter
    document.getElementById('promotionSearch').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#promotionTable tbody tr');

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>
@endsection
