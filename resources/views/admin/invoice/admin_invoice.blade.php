@extends('layouts.admin')

@section('title', 'Manage Invoices')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <div class="bg-[#1F2937] shadow-lg rounded-xl p-4 sm:p-6 border border-[#374151]">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-6 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2 w-full sm:w-auto">
                <i class="fas fa-file-invoice text-[#9CA3AF]"></i> Manage Invoices
            </h1>
            <div class="flex gap-4 w-full sm:w-auto">
                <a href="{{ route('admin.invoice.export') }}" class="bg-[#374151] hover:bg-[#4B5563] text-white font-semibold flex items-center gap-2 px-4 py-2 rounded-lg shadow transition-colors w-full sm:w-auto justify-center">
                    <i class="fas fa-file-export"></i> <span class="sm:inline">Export</span>
                </a>
            </div>
        </div>

        <div class="mb-6 flex flex-col sm:flex-row justify-between gap-4 items-center">
            <div class="relative w-full sm:w-1/3">
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Search Invoice..." 
                    class="w-full pl-10 pr-4 py-2 bg-[#374151] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] text-sm sm:text-base"
                >
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]"></i>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <select id="filterType" class="bg-[#374151] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] text-sm sm:text-base px-3 py-2">
                    <option value="">All Types</option>
                    <option value="product">Products</option>
                    <option value="subscription">Subscriptions</option>
                </select>
                <input 
                    type="date" 
                    id="dateFilter"
                    class="bg-[#374151] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] text-sm sm:text-base px-3 py-2"
                >
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-sm -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
            <table class="min-w-full divide-y divide-[#374151] text-xs sm:text-sm text-left" id="invoiceTable">
                <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-xs sticky top-0 z-10">
                    <tr>
                        <th class="px-3 sm:px-4 py-3">Invoice Number</th>
                        <th class="px-3 sm:px-4 py-3">Client</th>
                        <th class="px-3 sm:px-4 py-3">Type</th>
                        <th class="px-3 sm:px-4 py-3">Amount</th>
                        <th class="px-3 sm:px-4 py-3">Date</th>
                        <th class="px-3 sm:px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#374151]">
                    @forelse ($invoices as $invoice)
                        <tr class="hover:bg-[#374151] transition-colors">
                            <td class="px-3 sm:px-4 py-3 font-mono text-white text-xs sm:text-sm">{{ $invoice->invoice_number }}</td>
                            <td class="px-3 sm:px-4 py-3 font-medium text-white text-xs sm:text-sm">
                                {{ $invoice->user ? $invoice->user->full_name : 'WALKIN-GUEST' }}
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm">
                                <span class="px-2 py-1 rounded-full text-xs {{ $invoice->type === 'subscription' ? 'bg-blue-900 text-blue-200' : 'bg-green-900 text-green-200' }}">
                                    {{ ucfirst($invoice->type) }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-white text-xs sm:text-sm">₱{{ number_format($invoice->total_amount, 2) }}</td>
                            <td class="px-3 sm:px-4 py-3 text-[#9CA3AF] text-xs sm:text-sm">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</td>
                            <td class="px-3 sm:px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a 
                                        href="{{ route('admin.invoice.show', $invoice->id) }}" 
                                        class="inline-flex items-center gap-1 sm:gap-2 text-[#9CA3AF] hover:text-white font-medium transition-colors text-xs sm:text-sm"
                                        title="View Invoice"
                                    >
                                        <i class="fas fa-eye"></i> <span class="hidden sm:inline">View</span>
                                    </a>
                                    <a 
                                        href="{{ route('admin.invoice.print', $invoice->id) }}" 
                                        class="inline-flex items-center gap-1 sm:gap-2 text-[#9CA3AF] hover:text-white font-medium transition-colors text-xs sm:text-sm"
                                        title="Print Receipt"
                                    >
                                        <i class="fas fa-print"></i> <span class="hidden sm:inline">Print</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 sm:px-4 py-6 text-center text-[#9CA3AF]">
                                No invoices found. All transactions will appear here.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>

<script>
    // Client-side search and filtering
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const filterType = document.getElementById('filterType');
        const dateFilter = document.getElementById('dateFilter');
        const rows = document.querySelectorAll('#invoiceTable tbody tr');
        
        const filterTable = () => {
            const searchTerm = searchInput.value.toLowerCase();
            const typeFilter = filterType.value.toLowerCase();
            const dateValue = dateFilter.value;
            
            rows.forEach(row => {
                const invoiceNumber = row.cells[0].textContent.toLowerCase();
                const clientName = row.cells[1].textContent.toLowerCase();
                const type = row.cells[2].textContent.toLowerCase();
                const date = row.cells[4].textContent;
                
                // Parse the date for comparison
                let shouldShow = true;
                
                // Check search term
                if (searchTerm && !invoiceNumber.includes(searchTerm) && !clientName.includes(searchTerm)) {
                    shouldShow = false;
                }
                
                // Check type filter
                if (typeFilter && !type.includes(typeFilter)) {
                    shouldShow = false;
                }
                
                // Check date filter (simplified)
                if (dateValue) {
                    const rowDate = new Date(date);
                    const filterDate = new Date(dateValue);
                    
                    if (rowDate.toDateString() !== filterDate.toDateString()) {
                        shouldShow = false;
                    }
                }
                
                row.style.display = shouldShow ? '' : 'none';
            });
        };
        
        searchInput.addEventListener('input', filterTable);
        filterType.addEventListener('change', filterTable);
        dateFilter.addEventListener('input', filterTable);
    });
</script>
@endsection
