@extends('layouts.admin')

@section('title', 'Manage Trainers')

@section('content')
<div class="bg-white shadow-md rounded-xl p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-3xl font-semibold text-gray-800">Manage Trainers</h2>
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow flex items-center gap-2">
            <i class="fas fa-user-plus"></i> Add Trainer
        </button>
    </div>

    <div class="mb-4">
        <div class="relative w-full sm:w-1/3">
            <input 
                type="text" 
                placeholder="Search trainers..." 
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg">
        <table class="min-w-full text-base text-left text-gray-600 table-auto">
            <thead class="bg-gray-100 text-sm uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-4">Image</th>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Schedule</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Phone</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $trainers = [
                        ['img' => 'trainer1.jpg', 'name' => 'John Flex', 'schedule' => 'MWF 7AM-10AM', 'email' => 'john@example.com', 'phone' => '0917-123-4567'],
                        ['img' => 'trainer2.jpg', 'name' => 'Anna Power', 'schedule' => 'TTH 8AM-12NN', 'email' => 'anna@example.com', 'phone' => '0918-765-4321'],
                        ['img' => 'trainer3.jpg', 'name' => 'Carlos Speed', 'schedule' => 'Weekends 9AM-1PM', 'email' => 'carlos@example.com', 'phone' => '0921-987-6543'],
                    ];
                @endphp

                @foreach ($trainers as $trainer)
                    <tr class="bg-white border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <img 
                                src="{{ asset('assets/' . $trainer['img']) }}" 
                                alt="{{ $trainer['name'] }}" 
                                class="w-16 h-16 object-cover rounded-md ring-2 ring-gray-200"
                            >
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $trainer['name'] }}</td>
                        <td class="px-6 py-4">{{ $trainer['schedule'] }}</td>
                        <td class="px-6 py-4">{{ $trainer['email'] }}</td>
                        <td class="px-6 py-4">{{ $trainer['phone'] }}</td>
                        <td class="px-6 py-4 text-center space-x-3">
                            <a href="#" class="text-green-600 hover:text-green-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="text-blue-600 hover:text-blue-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="text-red-600 hover:text-red-800" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
