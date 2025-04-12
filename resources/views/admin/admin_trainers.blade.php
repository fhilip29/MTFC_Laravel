@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-10">
    <div class="flex justify-between mb-6">
        <input
            type="text"
            id="trainerSearch"
            placeholder="Search Trainers"
            class="border border-gray-300 rounded px-4 py-2 w-80"
        />
        <button
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded flex items-center gap-2"
        >
            <i class="fas fa-plus"></i> Add Trainer
        </button>
    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-100 uppercase font-bold">
                <tr>
                    <th class="px-6 py-4">Image</th>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Schedule</th>
                    <th class="px-6 py-4 text-center">Email</th>
                    <th class="px-6 py-4 text-center">Phone</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="trainersTableBody">
                {{-- Sample trainer row --}}
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <img src="{{ asset('assets/default-profile.png') }}" alt="Trainer" class="h-16 w-16 rounded object-cover">
                    </td>
                    <td class="px-6 py-4">John Doe</td>
                    <td class="px-6 py-4">Mon, Wed, Fri<br>10AM - 2PM</td>
                    <td class="px-6 py-4 text-center">john@example.com</td>
                    <td class="px-6 py-4 text-center">+639123456789</td>
                    <td class="px-6 py-4 text-center">
                        <button class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
                {{-- You can loop trainers here when backend is ready --}}
            </tbody>
        </table>
    </div>
</div>
@endsection
