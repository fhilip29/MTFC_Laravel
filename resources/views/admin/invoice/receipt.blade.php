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
        .prominent-receipt-number {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border: 1px dashed #ddd;
            word-break: break-all;
            letter-spacing: 0.5px;
        }
        .receipt-number {
            text-align: center;
            width: 100%;
            display: block;
            margin: 10px 0;
            word-break: break-all;
            font-size: 12px;
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
        /* Button styles */
        .button-container {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 15px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            min-width: 140px;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0069d9;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-custom {
            background-color: #FA5455;
            color: white;
            width: 180px;
        }
        .btn-custom:hover {
            background-color: #e84142;
        }
        .btn i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="no-print button-container">
        <button onclick="window.print()" class="btn btn-custom">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        <a href="#" onclick="downloadPDF()" class="btn btn-custom">
            <i class="fas fa-download"></i> Download PDF
        </a>
    </div>
    
    <div class="receipt">
        <div class="receipt-header">
            <h1>MTFC FITNESS</h1>
            <p>Manila Total Fitness Center</p>
            <p>Bldg, 3rd Floor, 350, YMCA, 1000 Antonio Villegas St, Ermita, Manila, 1000 Metro Manila</p>
            <p>Tel: 09985585911</p>
            <p>Email: mtfc987@gmail.com</p>
        </div>
        
        <div class="receipt-details">
            <div class="invoice-info" style="text-align: center; margin-bottom: 15px;">
                <div style="width: 100%;">
                    <strong>Receipt #:</strong> {{ $invoice->invoice_number }}
                </div>
            </div>
            
            <div class="invoice-info">
                <div>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y h:i A') }}
                </div>
            </div>
            
            <div class="invoice-info">
                <div>
                    <strong>Payment Method:</strong> {{ ucfirst($invoice->payment_method ?: 'Online') }}
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
            <strong>Payment Method:</strong> {{ ucfirst($invoice->payment_method ?: 'Online') }}<br>
            <strong>Payment Status:</strong> Paid
        </div>
        
        <div class="prominent-receipt-number">
            {{ $invoice->invoice_number }}
        </div>
        
        <div class="receipt-info" style="text-align: center;">
            This receipt serves as proof of payment. Please keep for your records.
        </div>
        
        <div class="receipt-footer">
            <p>Thank you for choosing MTFC Fitness!</p>
            <p>We look forward to helping you achieve your fitness goals.</p>
            <p>mtfc987@gmail.com</p>
        </div>
    </div>

    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- html2pdf.js for PDF download functionality -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        // Function to download the receipt as PDF
        function downloadPDF() {
            // Get the receipt element
            const element = document.querySelector('.receipt');
            
            // Configure html2pdf options
            const opt = {
                margin:       [5, 0, 5, 0],
                filename:     'receipt-{{ substr($invoice->invoice_number, 0, 8) }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, logging: false },
                jsPDF:        { unit: 'mm', format: [80, 297], orientation: 'portrait' },
                pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
            };
            
            // Generate and download PDF
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>