@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center text-gray-700 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
        
        <!-- Payment Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Payment Details</h1>
                    <p class="text-gray-500 text-sm">Receipt #{{ Str::limit($invoice->invoice_number, 12) }}</p>
                </div>
                <div>
                    <a 
                        href="{{ route('user.payments.receipt', $invoice->id) }}" 
                        class="bg-gray-800 hover:bg-gray-700 text-white py-2 px-4 rounded-lg text-sm inline-flex items-center gap-2"
                        target="_blank"
                    >
                        <i class="fas fa-download"></i> Download Receipt
                    </a>
                </div>
            </div>
            
            <!-- Payment Summary -->
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <div class="text-gray-500 uppercase tracking-wider text-xs">Receipt No.</div>
                        <div class="text-gray-800 font-mono mt-1 break-all">{{ $invoice->invoice_number }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 uppercase tracking-wider text-xs">Date</div>
                        <div class="text-gray-800 mt-1">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 uppercase tracking-wider text-xs">Type</div>
                        <div class="mt-1">
                            <span class="px-2 py-1 rounded-full text-xs {{ $invoice->type === 'subscription' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($invoice->type) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="text-gray-500 uppercase tracking-wider text-xs">Status</div>
                        <div class="mt-1">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Paid</span>
                        </div>
                    </div>
                </div>
                
                <!-- Items Table -->
                <div class="mb-6">
                    <h2 class="text-base font-semibold text-gray-800 mb-4">Items Purchased</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-gray-800 text-sm">
                            <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                                <tr>
                                    <th class="py-2 px-3 text-left font-medium">Item</th>
                                    <th class="py-2 px-3 text-left font-medium">Description</th>
                                    <th class="py-2 px-3 text-center font-medium">Quantity</th>
                                    <th class="py-2 px-3 text-right font-medium">Unit Price</th>
                                    <th class="py-2 px-3 text-right font-medium">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-3">
                                        @if($item['product_image'])
                                            <div class="flex items-center">
                                                <img src="{{ $item['product_image'] }}" alt="{{ $item['description'] }}" class="w-12 h-12 object-cover rounded-md">
                                            </div>
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded-md flex items-center justify-center">
                                                <i class="fas fa-box text-gray-400"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">{{ $item['description'] }}</td>
                                    <td class="py-3 px-3 text-center">{{ $item['quantity'] }}</td>
                                    <td class="py-3 px-3 text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                                    <td class="py-3 px-3 text-right font-medium">₱{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t-2 border-gray-200">
                                <tr>
                                    <td colspan="4" class="pt-3 pb-2 px-3 text-right font-semibold text-gray-600">Total:</td>
                                    <td class="pt-3 pb-2 px-3 text-right font-bold text-lg text-red-600">₱{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <!-- Footer Note -->
                <div class="text-xs text-gray-500 text-center mt-8 border-t border-gray-200 pt-4">
                    Thank you for your purchase at Manila Total Fitness Center.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 