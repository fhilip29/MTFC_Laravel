@extends('layouts.admin') 

@section('title', 'Manage Invoices')

@section('content')
<div class="bg-white shadow-lg rounded-xl p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h2 class="text-3xl font-bold text-gray-800">🧾 Manage Invoices</h2>
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold flex items-center gap-2 px-4 py-2 rounded-lg shadow">
            <i class="fas fa-plus"></i> Add Invoice
        </button>
    </div>

    <div class="mb-4">
        <div class="relative w-full sm:w-1/3">
            <input 
                type="text" 
                placeholder="Search Invoice..." 
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg shadow-sm">
        <table class="min-w-full table-auto text-base text-left text-gray-600">
            <thead class="bg-gray-100 text-sm uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-4">Invoice ID</th>
                    <th class="px-6 py-4">Client</th>
                    <th class="px-6 py-4">Invoice Date</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $invoices = [
                        ['id' => '67fcfad2e72b837c39c95f0e', 'client' => 'WALKIN-GUEST', 'date' => '2025-04-14'],
                        ['id' => '67f0672c2c3835a10475a18e9', 'client' => 'King Dranreb Languido', 'date' => '2025-04-05'],
                        ['id' => '67f0615bc3835a10475a18a7', 'client' => 'King Dranreb Languido', 'date' => '2025-04-05'],
                        ['id' => '67f0611bc3835a10475a18a1', 'client' => 'WALKIN-GUEST', 'date' => '2025-04-05'],
                        ['id' => '67f05cc03eae00b1b28906c9', 'client' => 'WALKIN-GUEST', 'date' => '2025-04-05'],
                    ];
                @endphp

                @foreach ($invoices as $invoice)
                    <tr class="bg-white border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $invoice['id'] }}</td>
                        <td class="px-6 py-4">{{ $invoice['client'] }}</td>
                        <td class="px-6 py-4">{{ $invoice['date'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <a 
                                href="#" 
                                class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium"
                                title="View Invoice"
                            >
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
