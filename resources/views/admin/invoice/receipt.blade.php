<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: 80mm 297mm;
            margin: 0;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            width: 80mm;
            margin: 0 auto;
        }
        .receipt {
            padding: 10px;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 10px;
        }
        .receipt-header h1 {
            font-size: 16px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .receipt-header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .receipt-details {
            margin-bottom: 15px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .invoice-info div {
            font-size: 11px;
        }
        .client-info {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            text-align: left;
            font-size: 11px;
            padding: 5px 0;
            border-bottom: 1px solid #ccc;
        }
        td {
            padding: 5px 0;
            border-bottom: 1px dashed #eee;
            font-size: 11px;
        }
        .amount {
            text-align: right;
        }
        .total-row td {
            border-top: 1px solid #ccc;
            border-bottom: double 3px #ccc;
            font-weight: bold;
            padding-top: 5px;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
        .receipt-barcode {
            text-align: center;
            margin: 15px 0;
        }
        .receipt-barcode img {
            max-width: 100%;
            height: 40px;
        }
        .receipt-info {
            font-style: italic;
            font-size: 10px;
            margin: 10px 0;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                width: 100%;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; padding: 10px;">
        <button onclick="window.print()">Print Receipt</button>
        <a href="{{ route('admin.invoice.show', $invoice->id) }}">Back to Invoice</a>
    </div>
    
    <div class="receipt">
        <div class="receipt-header">
            <h1>MTFC FITNESS</h1>
            <p>Move Together Fitness Center</p>
            <p>123 Main Street, Cityville, Philippines</p>
            <p>Tel: (123) 456-7890</p>
            <p>Email: info@mtfcfitness.com</p>
        </div>
        
        <div class="receipt-details">
            <div class="invoice-info">
                <div>
                    <strong>Receipt #:</strong> {{ substr($invoice->invoice_number, 0, 8) }}
                </div>
                <div>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y h:i A') }}
                </div>
            </div>
            
            <div class="client-info">
                <strong>{{ $invoice->user ? $invoice->user->full_name : 'WALKIN-GUEST' }}</strong><br>
                @if($invoice->user)
                {{ $invoice->user->email }}<br>
                {{ $invoice->user->mobile_number ?? 'No phone' }}
                @endif
            </div>
            
            <div style="font-size: 10px; margin-bottom: 5px; text-transform: uppercase;">
                <strong>{{ ucfirst($invoice->type) }} Receipt</strong>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="amount">₱{{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
                
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td class="amount"><strong>₱{{ number_format($invoice->total_amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        <div style="text-align: center; font-size: 11px; margin: 10px 0;">
            <strong>Payment Method:</strong> Cash<br>
            <strong>Payment Status:</strong> Paid
        </div>
        
        <div class="receipt-barcode">
            <!-- Placeholder for barcode -->
            <svg height="30" width="100%" style="border: 1px dashed #ccc;">
                <rect width="100%" height="100%" fill="white" />
                <text x="50%" y="50%" text-anchor="middle" alignment-baseline="middle" font-size="12">
                    {{ $invoice->invoice_number }}
                </text>
            </svg>
        </div>
        
        <div class="receipt-info">
            This receipt serves as proof of payment. Please keep for your records.
        </div>
        
        <div class="receipt-footer">
            <p>Thank you for choosing MTFC Fitness!</p>
            <p>We look forward to helping you achieve your fitness goals.</p>
            <p>www.mtfcfitness.com</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Auto print when loaded (optional)
            // window.print();
        }
    </script>
</body>
</html> 