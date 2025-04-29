@extends('layouts.admin')

@section('title', 'Invoice Details')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <div class="bg-[#1F2937] shadow-lg rounded-xl p-4 sm:p-6 border border-[#374151]">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.invoice.invoice') }}" class="text-[#9CA3AF] hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-file-invoice text-[#9CA3AF]"></i> Invoice Details
                </h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.invoice.print', $invoice->id) }}" class="bg-[#374151] hover:bg-[#4B5563] text-white font-semibold flex items-center gap-2 px-4 py-2 rounded-lg shadow transition-colors">
                    <i class="fas fa-print"></i> <span class="hidden sm:inline">Print</span>
                </a>
            </div>
        </div>

        <!-- Invoice Header Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-[#374151] p-4 rounded-lg">
                <h2 class="text-[#9CA3AF] text-sm font-semibold uppercase mb-2">Invoice Information</h2>
                <div class="space-y-2 text-white">
                    <div class="flex justify-between">
                        <span class="text-[#9CA3AF]">Invoice Number:</span>
                        <span class="font-mono">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#9CA3AF]">Date:</span>
                        <span>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#9CA3AF]">Type:</span>
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $invoice->type === 'subscription' ? 'bg-blue-900 text-blue-200' : 'bg-green-900 text-green-200' }}">
                            {{ ucfirst($invoice->type) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="bg-[#374151] p-4 rounded-lg">
                <h2 class="text-[#9CA3AF] text-sm font-semibold uppercase mb-2">Client Information</h2>
                <div class="space-y-2 text-white">
                    <div class="flex justify-between">
                        <span class="text-[#9CA3AF]">Name:</span>
                        <span>{{ $invoice->user ? $invoice->user->full_name : 'WALKIN-GUEST' }}</span>
                    </div>
                    @if($invoice->user)
                    <div class="flex justify-between">
                        <span class="text-[#9CA3AF]">Email:</span>
                        <span>{{ $invoice->user->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#9CA3AF]">Phone:</span>
                        <span>{{ $invoice->user->mobile_number ?? 'N/A' }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="mb-8">
            <h2 class="text-[#9CA3AF] text-sm font-semibold uppercase mb-4">Invoice Items</h2>
            <div class="overflow-x-auto rounded-lg">
                <table class="min-w-full divide-y divide-[#374151]">
                    <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Description</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#374151]">
                        @foreach($invoice->items as $item)
                        <tr class="hover:bg-[#374151] transition-colors">
                            <td class="px-4 py-3 text-white">{{ $item->description }}</td>
                            <td class="px-4 py-3 text-white text-right">₱{{ number_format($item->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-[#374151]">
                        <tr>
                            <td class="px-4 py-3 text-white font-semibold text-right">Total</td>
                            <td class="px-4 py-3 text-white font-semibold text-right">₱{{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="bg-[#374151] p-4 rounded-lg mb-6">
            <h2 class="text-[#9CA3AF] text-sm font-semibold uppercase mb-2">Payment Information</h2>
            <div class="space-y-2 text-white">
                <div class="flex justify-between">
                    <span class="text-[#9CA3AF]">Payment Method:</span>
                    <span>Cash</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#9CA3AF]">Payment Status:</span>
                    <span class="px-2 py-0.5 rounded-full text-xs bg-green-900 text-green-200">Paid</span>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-[#374151] p-4 rounded-lg">
            <h2 class="text-[#9CA3AF] text-sm font-semibold uppercase mb-2">Notes</h2>
            <p class="text-white text-sm">
                Thank you for your business! This is an official receipt of your transaction.
            </p>
        </div>
    </div>
</div>
@endsection 