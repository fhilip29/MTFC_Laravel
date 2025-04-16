@extends('layouts.admin')

@section('title', 'Manage Invoices')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <div class="bg-[#1F2937] shadow-lg rounded-xl p-4 sm:p-6 border border-[#374151]">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-6 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2 w-full sm:w-auto">
                <i class="fas fa-file-invoice text-[#9CA3AF]"></i> Manage Invoices
            </h1>
            <button class="bg-[#374151] hover:bg-[#4B5563] text-white font-semibold flex items-center gap-2 px-4 py-2 rounded-lg shadow transition-colors w-full sm:w-auto justify-center">
                <i class="fas fa-plus"></i> <span class="sm:inline">Add Invoice</span>
            </button>
        </div>

        <div class="mb-6">
            <div class="relative w-full sm:w-1/3">
                <input 
                    type="text" 
                    placeholder="Search Invoice..." 
                    class="w-full pl-10 pr-4 py-2 bg-[#374151] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] text-sm sm:text-base"
                >
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]"></i>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-sm -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
            <table class="min-w-full divide-y divide-[#374151] text-xs sm:text-sm text-left" id="invoiceTable">
                <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-xs sticky top-0 z-10">
                    <tr>
                        <th class="px-3 sm:px-4 py-3">Invoice ID</th>
                        <th class="px-3 sm:px-4 py-3">Client</th>
                        <th class="px-3 sm:px-4 py-3">Invoice Date</th>
                        <th class="px-3 sm:px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#374151]">
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
                        <tr class="hover:bg-[#374151] transition-colors">
                            <td class="px-3 sm:px-4 py-3 font-mono text-white text-xs sm:text-sm">{{ $invoice['id'] }}</td>
                            <td class="px-3 sm:px-4 py-3 font-medium text-white text-xs sm:text-sm">{{ $invoice['client'] }}</td>
                            <td class="px-3 sm:px-4 py-3 text-[#9CA3AF] text-xs sm:text-sm">{{ $invoice['date'] }}</td>
                            <td class="px-3 sm:px-4 py-3 text-center">
                                <a 
                                    href="#" 
                                    class="inline-flex items-center gap-1 sm:gap-2 text-[#9CA3AF] hover:text-white font-medium transition-colors text-xs sm:text-sm"
                                    title="View Invoice"
                                >
                                    <i class="fas fa-eye"></i> <span class="hidden sm:inline">View</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
