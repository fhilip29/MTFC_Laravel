@extends('layouts.admin')

@section('title', 'Invoice Details')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-6">
    <div class="bg-[#1F2937] shadow-lg rounded-xl p-3 sm:p-5 border border-[#374151]">
        <!-- Header with back button and actions -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.invoice.invoice') }}" class="text-[#9CA3AF] hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-lg sm:text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-file-invoice text-[#9CA3AF]"></i> Invoice #{{ $invoice->invoice_number }}
                </h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.invoice.print', $invoice->id) }}" class="bg-[#374151] hover:bg-[#4B5563] text-white font-medium flex items-center gap-1 px-3 py-1.5 rounded-md shadow transition-colors text-sm">
                    <i class="fas fa-print"></i> <span class="hidden sm:inline">Print</span>
                </a>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4">
            <!-- Left column - Invoice details -->
            <div class="w-full md:w-1/3 space-y-3">
                <!-- Invoice Information Card -->
                <div class="bg-[#374151] p-3 rounded-lg">
                    <div class="flex justify-between items-center border-b border-[#4B5563] pb-2 mb-2">
                        <h2 class="text-[#9CA3AF] text-xs font-semibold uppercase">Invoice Info</h2>
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $invoice->type === 'subscription' ? 'bg-blue-900 text-blue-200' : 'bg-green-900 text-green-200' }}">
                            {{ ucfirst($invoice->type) }}
                        </span>
                    </div>
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-[#9CA3AF]">Date:</span>
                            <span class="text-white">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#9CA3AF]">Status:</span>
                            <span class="text-green-400">Paid</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#9CA3AF]">Method:</span>
                            <span class="text-white">Cash</span>
                        </div>
                    </div>
                </div>

                <!-- Client Information Card -->
                @if($invoice->user)
                <div class="bg-[#374151] p-3 rounded-lg">
                    <div class="border-b border-[#4B5563] pb-2 mb-2">
                        <h2 class="text-[#9CA3AF] text-xs font-semibold uppercase">Client</h2>
                    </div>
                    <div class="space-y-1.5 text-sm">
                        <div class="text-white font-medium">{{ $invoice->user->full_name }}</div>
                        <div class="text-[#9CA3AF]">{{ $invoice->user->email }}</div>
                        @if($invoice->user->mobile_number)
                            <div class="text-[#9CA3AF]">{{ $invoice->user->mobile_number }}</div>
                        @endif
                    </div>
                </div>
                @else
                <div class="bg-[#374151] p-3 rounded-lg">
                    <div class="border-b border-[#4B5563] pb-2 mb-2">
                        <h2 class="text-[#9CA3AF] text-xs font-semibold uppercase">Client</h2>
                    </div>
                    <div class="text-white font-medium">WALKIN-GUEST</div>
                </div>
                @endif
            </div>

            <!-- Right column - Items and total -->
            <div class="w-full md:w-2/3">
                <div class="bg-[#374151] rounded-lg overflow-hidden">
                    <div class="p-3 border-b border-[#4B5563]">
                        <h2 class="text-[#9CA3AF] text-xs font-semibold uppercase">Items</h2>
                    </div>
                    
                    <div class="overflow-x-auto max-h-80">
                        <table class="min-w-full divide-y divide-[#4B5563]">
                            <thead class="bg-[#2D3748] sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-[#9CA3AF]">Description</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-[#9CA3AF] w-24">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#4B5563]">
                                @foreach($invoice->items as $item)
                                <tr class="hover:bg-[#2D3748] transition-colors">
                                    <td class="px-3 py-2 text-white text-sm">{{ $item->description }}</td>
                                    <td class="px-3 py-2 text-white text-right text-sm">₱{{ number_format($item->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-3 border-t border-[#4B5563] bg-[#2D3748]">
                        <div class="flex justify-between items-center">
                            <span class="text-white font-medium">Total</span>
                            <span class="text-white font-bold text-lg">₱{{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Notes section -->
                <div class="mt-3 bg-[#374151] p-3 rounded-lg">
                    <p class="text-[#9CA3AF] text-xs">
                        Thank you for your business! This is an official receipt of your transaction.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom scrollbar for invoice items table */
    .max-h-80::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }
    
    .max-h-80::-webkit-scrollbar-track {
        background: #2D3748;
    }
    
    .max-h-80::-webkit-scrollbar-thumb {
        background: #4B5563;
        border-radius: 4px;
    }
    
    .max-h-80::-webkit-scrollbar-thumb:hover {
        background: #6B7280;
    }
    
    /* Firefox scrollbar */
    .max-h-80 {
        scrollbar-width: thin;
        scrollbar-color: #4B5563 #2D3748;
    }
</style>
@endsection 