@extends('layouts.admin')

@section('title', 'Manage Members')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-700 flex items-center gap-2">
            <i class="fas fa-users text-blue-500"></i> Manage Members
        </h2>

        <div class="relative w-1/3">
            <input
                type="text"
                placeholder="Search members..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none"
                id="searchInput"
            >
            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
        </div>
    </div>

    <div class="overflow-x-auto rounded-md">
        <table class="min-w-full text-sm text-left text-gray-600" id="membersTable">
            <thead class="text-xs uppercase bg-gray-100 text-gray-600">
                <tr>
                    <th class="px-6 py-3">Photo</th>
                    <th class="px-6 py-3">Full Name</th>
                    <th class="px-6 py-3">Gender</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @php
                    $members = [
                        ['img' => 'MTFC_LOGO.PNG', 'name' => 'King Dranreb Languido', 'gender' => 'male', 'email' => 'test2@gmail.com'],
                        ['img' => 'lock.png', 'name' => 'Admin MTFC', 'gender' => 'male', 'email' => 'admin@gmail.com'],
                        ['img' => 'kassandra.jpg', 'name' => 'Kassandra Singson', 'gender' => 'female', 'email' => 'kassandra10singson@gmail.com'],
                        ['img' => 'philip.jpg', 'name' => 'Philip Lorenzo', 'gender' => 'male', 'email' => 'lorenzophilip7@gmail.com'],
                    ];
                @endphp

                @foreach ($members as $member)
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="px-6 py-4">
                            <img
                                src="{{ asset('assets/' . $member['img']) }}"
                                alt="{{ $member['name'] }}"
                                class="w-10 h-10 rounded-full object-cover border"
                                onerror="this.onerror=null;this.src='{{ asset('assets/default.png') }}';"
                            >
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $member['name'] }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                                {{ strtolower($member['gender']) === 'male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                {{ ucfirst($member['gender']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $member['email'] }}</td>
                        <td class="px-6 py-4 text-center space-x-3">
                        <a href="#" class="text-blue-600 hover:text-blue-800" title="Edit Member">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="text-red-600 hover:text-red-800" title="Delete Member">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    // Live Search Functionality
    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#membersTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>
@endsection
