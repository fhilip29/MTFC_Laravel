<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index()
    {
        $invoices = Invoice::with('user')->latest()->paginate(15);
        return view('admin.invoice.admin_invoice', compact('invoices'));
    }

    /**
     * Display the specified invoice
     */
    public function show($id)
    {
        $invoice = Invoice::with(['user', 'items'])->findOrFail($id);
        return view('admin.invoice.invoice_details', compact('invoice'));
    }

    /**
     * Store a product purchase invoice
     */
    public function storeProductInvoice($userId, $items, $totalAmount)
    {
        $invoice = Invoice::create([
            'invoice_number' => (string) Str::uuid(),
            'user_id' => $userId,
            'type' => 'product',
            'total_amount' => $totalAmount,
            'invoice_date' => now()->format('Y-m-d'),
        ]);

        foreach ($items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['name'],
                'amount' => $item['price'] * $item['quantity'],
            ]);
        }

        return $invoice;
    }

    /**
     * Store a subscription invoice
     */
    public function storeSubscriptionInvoice($userId, $subscriptionDetails, $amount)
    {
        $invoice = Invoice::create([
            'invoice_number' => (string) Str::uuid(),
            'user_id' => $userId,
            'type' => 'subscription',
            'total_amount' => $amount,
            'invoice_date' => now()->format('Y-m-d'),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $subscriptionDetails,
            'amount' => $amount,
        ]);

        return $invoice;
    }

    /**
     * Generate a printable receipt for an invoice
     */
    public function printReceipt($id)
    {
        $invoice = Invoice::with(['user', 'items'])->findOrFail($id);
        return view('admin.invoice.receipt', compact('invoice'));
    }

    /**
     * Export invoices as CSV
     */
    public function export(Request $request)
    {
        $fileName = 'invoices_' . now()->format('Y-m-d') . '.csv';
        
        $invoices = Invoice::with(['user', 'items'])
            ->when($request->filled('start_date') && $request->filled('end_date'), function($query) use ($request) {
                $query->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
            })
            ->when($request->filled('type'), function($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->latest()
            ->get();
            
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        
        $columns = [
            'Invoice Number', 
            'Date', 
            'Type', 
            'Client', 
            'Email', 
            'Items', 
            'Total Amount'
        ];
        
        $callback = function() use($invoices, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($invoices as $invoice) {
                $itemDescriptions = $invoice->items->pluck('description')->implode(', ');
                
                $row = [
                    $invoice->invoice_number,
                    $invoice->invoice_date,
                    ucfirst($invoice->type),
                    $invoice->user ? $invoice->user->full_name : 'WALKIN-GUEST',
                    $invoice->user ? $invoice->user->email : 'N/A',
                    $itemDescriptions,
                    number_format($invoice->total_amount, 2),
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display the specified invoice for the authenticated user
     */
    public function userShow($id)
    {
        $invoice = Invoice::with(['user', 'items'])
            ->where('user_id', auth()->id()) // Only show invoices owned by the user
            ->findOrFail($id);
        
        return view('invoice_details', compact('invoice'));
    }

    /**
     * Display a printable receipt for the authenticated user
     */
    public function userShowReceipt($id)
    {
        $invoice = Invoice::with(['user', 'items'])
            ->where('user_id', auth()->id()) // Ensure invoice belongs to the logged-in user
            ->findOrFail($id);
            
        // Reuse the admin receipt view
        return view('admin.invoice.receipt', compact('invoice'));
    }
} 