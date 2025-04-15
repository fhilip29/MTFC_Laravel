@extends('layouts.admin')

@section('title', 'Gym Equipment Management')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-md">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">🏋️ Gym Equipment Management</h1>
    </div>

    <!-- Search and Add -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <input
            type="text"
            placeholder="Search Equipment"
            class="w-full md:w-1/3 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
        />

        <a href="#"
           class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus"></i> Add Equipment
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border border-gray-200 text-sm rounded-xl overflow-hidden">
            <thead class="bg-gray-100 text-gray-700 text-left">
                <tr>
                    <th class="py-3 px-4">Equipment Name</th>
                    <th class="py-3 px-4">Contact</th>
                    <th class="py-3 px-4">Vendor</th>
                    <th class="py-3 px-4">Purchase Date</th>
                    <th class="py-3 px-4">Quality</th>
                    <th class="py-3 px-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-800">
                @php
                    $equipments = [
                        [
                            'name' => 'Inclined Press',
                            'contact' => '09171155987',
                            'vendor' => 'Arnold Swahehshs',
                            'date' => '03-05-2025',
                            'quality' => 'Broken',
                            'quality_class' => 'bg-red-500',
                        ],
                        [
                            'name' => 'Bench Press',
                            'contact' => '09171055687',
                            'vendor' => 'Arnold Swahehshs',
                            'date' => '03-06-2025',
                            'quality' => 'Good',
                            'quality_class' => 'bg-green-400',
                        ],
                        [
                            'name' => 'Pull Machine',
                            'contact' => '09171155987',
                            'vendor' => 'Gyms Gold',
                            'date' => '03-05-2025',
                            'quality' => 'Rusty',
                            'quality_class' => 'bg-orange-500',
                        ],
                    ];
                @endphp

                @foreach($equipments as $equipment)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium">{{ $equipment['name'] }}</td>
                        <td class="py-3 px-4">{{ $equipment['contact'] }}</td>
                        <td class="py-3 px-4">{{ $equipment['vendor'] }}</td>
                        <td class="py-3 px-4">{{ $equipment['date'] }}</td>
                        <td class="py-3 px-4">
                            <span class="text-white text-sm px-3 py-1 rounded-full font-semibold {{ $equipment['quality_class'] }}">
                                {{ $equipment['quality'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="#" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-pen"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
