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
     * Store a product invoice
     */
    public function storeProductInvoice($userId, $items, $amount)
    {
        // Log product invoice creation
        \Log::info('Creating product invoice', [
            'user_id' => $userId,
            'items_count' => count($items),
            'amount' => $amount
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'user_id' => $userId,
            'type' => 'product',
            'total_amount' => $amount,
            'invoice_date' => now()->format('Y-m-d'),
            'payment_status' => 'completed',
            'payment_method' => isset($items[0]['payment_method']) ? $items[0]['payment_method'] : 'online'
        ]);

        \Log::info('Product invoice created', ['invoice_id' => $invoice->id, 'invoice_number' => $invoice->invoice_number]);

        // Create invoice items
        foreach ($items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['name'] ?? 'Product',
                'amount' => ($item['price'] ?? 0) * ($item['quantity'] ?? 1),
                'quantity' => $item['quantity'] ?? 1
            ]);
        }

        return $invoice;
    }

    /**
     * Store a subscription invoice
     */
    public function storeSubscriptionInvoice($userId, $subscriptionDetails, $amount, $paymentStatus = 'completed')
    {
        // Log subscription invoice creation
        \Log::info('Creating subscription invoice', [
            'user_id' => $userId,
            'details' => $subscriptionDetails,
            'amount' => $amount,
            'status' => $paymentStatus
        ]);

        // Extract subscription ID from details if available
        $subscriptionId = null;
        if (is_numeric($subscriptionDetails)) {
            $subscriptionId = $subscriptionDetails;
            $subscriptionDetails = \App\Models\Subscription::find($subscriptionId) ? 
                ucfirst(\App\Models\Subscription::find($subscriptionId)->type) . ' - ' . 
                ucfirst(\App\Models\Subscription::find($subscriptionId)->plan) . ' Plan' : 
                'Subscription Plan';
        }

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'user_id' => $userId,
            'type' => 'subscription',
            'total_amount' => $amount,
            'invoice_date' => now()->format('Y-m-d'),
            'payment_status' => $paymentStatus,
            'subscription_id' => $subscriptionId
        ]);

        \Log::info('Invoice created', ['invoice_id' => $invoice->id, 'invoice_number' => $invoice->invoice_number]);

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

    /**
     * API endpoint to get invoice items with product details
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoiceItems($id)
    {
        try {
            $invoice = Invoice::with('items')->where('id', $id)
                ->where('user_id', auth()->id()) // Ensure invoice belongs to the user
                ->firstOrFail();
            
            $items = [];
            
            foreach ($invoice->items as $item) {
                // Try to find product information if this is a product invoice
                $productInfo = null;
                $quantity = 1;
                
                // For product invoices, try to find the product
                if ($invoice->type === 'product') {
                    // Try to extract product ID from description or other means
                    // This depends on how you store product info in invoice items
                    $productName = $item->description;
                    
                    // Look for any order items with matching product name
                    $orderItem = \App\Models\OrderItem::whereHas('product', function($query) use ($productName) {
                        $query->where('name', 'like', '%' . $productName . '%');
                    })->first();
                    
                    if ($orderItem) {
                        $productInfo = $orderItem->product;
                        $quantity = $orderItem->quantity;
                    }
                }
                
                $itemData = [
                    'description' => $item->description,
                    'amount' => $item->amount,
                    'quantity' => $quantity,
                    'unit_price' => $quantity > 0 ? $item->amount / $quantity : $item->amount,
                ];
                
                // Add product data if available
                if ($productInfo) {
                    $itemData['product_id'] = $productInfo->id;
                    $itemData['product_image'] = $productInfo->image ? asset($productInfo->image) : null;
                }
                
                $items[] = $itemData;
            }
            
            return response()->json([
                'success' => true,
                'items' => $items,
                'invoice' => [
                    'number' => $invoice->invoice_number,
                    'date' => $invoice->invoice_date,
                    'total' => $invoice->total_amount,
                    'type' => $invoice->type
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting invoice items: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Could not retrieve invoice items'
            ], 404);
        }
    }

    /**
     * Display a list of all invoices for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function userInvoices(Request $request)
    {
        $query = Invoice::with('items')
            ->where('user_id', auth()->id());
            
        // Apply type filter if provided
        if ($request->has('type') && in_array($request->type, ['product', 'subscription'])) {
            $query->where('type', $request->type);
        }
        
        // Apply date filter if provided
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('invoice_date', [$request->date_from, $request->date_to]);
        } elseif ($request->has('date_from')) {
            $query->where('invoice_date', '>=', $request->date_from);
        } elseif ($request->has('date_to')) {
            $query->where('invoice_date', '<=', $request->date_to);
        }
        
        // Get the invoices with pagination
        $invoices = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
            
        return view('user.invoices', compact('invoices'));
    }

    /**
     * Display a detailed view of a specific invoice for the authenticated user
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function userInvoiceDetails($id)
    {
        $invoice = Invoice::with(['user', 'items'])
            ->where('user_id', auth()->id()) // Only show invoices owned by the user
            ->findOrFail($id);
            
        // Fetch additional product information for invoice items
        $items = [];
        foreach ($invoice->items as $item) {
            $productInfo = null;
            $quantity = 1;
            
            // Try to find product information if this is a product invoice
            if ($invoice->type === 'product') {
                // Try to extract product ID from description or other means
                $productName = $item->description;
                
                // Look for any order items with matching product name
                $orderItem = \App\Models\OrderItem::whereHas('product', function($query) use ($productName) {
                    $query->where('name', 'like', '%' . $productName . '%');
                })->first();
                
                if ($orderItem) {
                    $productInfo = $orderItem->product;
                    $quantity = $orderItem->quantity;
                }
            }
            
            $itemData = [
                'description' => $item->description,
                'amount' => $item->amount,
                'quantity' => $quantity,
                'unit_price' => $item->amount / $quantity,
                'product_image' => $productInfo ? asset($productInfo->image) : null,
            ];
            
            $items[] = $itemData;
        }
        
        return view('user.invoice_details', compact('invoice', 'items'));
    }
} 