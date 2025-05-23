<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use App\Http\Controllers\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $invoiceController;

    public function __construct(InvoiceController $invoiceController)
    {
        $this->invoiceController = $invoiceController;
    }

    /**
     * Display a listing of the user's orders
     */
    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders', ['orders' => $orders]);
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to place an order',
            ], 401);
        }

        // Prevent admins and trainers from placing orders
        $user = Auth::user();
        if ($user->role === 'admin' || $user->role === 'trainer') {
            return response()->json([
                'success' => false,
                'message' => 'Admin and trainer accounts cannot place orders. Please use a member account for shopping.',
            ], 403);
        }

        // Validate the request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone_number' => 'required|string|max:20',
            'payment_method' => 'required|string|max:50',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        // Log order items for debugging
        \Log::info('Order items received:', $validated['items']);
        
        // Use database transaction to ensure all operations succeed or fail together
        try {
            DB::beginTransaction();

            // Generate a unique reference number
            $referenceNo = 'ORD-' . strtoupper(Str::random(8));

            // Create the order
            $order = Order::create([
                'reference_no' => $referenceNo,
                'user_id' => Auth::id(),
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'street' => $validated['street'],
                'barangay' => $validated['barangay'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'phone_number' => $validated['phone_number'],
                'payment_method' => $validated['payment_method'],
                'status' => 'Pending', // Default status is Pending
                'order_date' => now(),
            ]);

            // Calculate total amount and prepare items for invoice
            $totalAmount = 0;
            $invoiceItems = [];
            $stockUpdateErrors = [];

            // Create order items
            foreach ($validated['items'] as $item) {
                // Add to order total
                $itemTotal = $item['price'] * $item['quantity'];
                $totalAmount += $itemTotal;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Update product stock (reserving stock when order is placed)
                $product = Product::find($item['id']);
                if ($product) {
                    // Add to invoice items
                    $invoiceItems[] = [
                        'id' => $item['id'],
                        'name' => $product->name,
                        'price' => $item['price'],
                        'quantity' => $item['quantity']
                    ];
                    
                    // Log product before stock update
                    \Log::info("Product before stock update - ID: {$product->id}, Name: {$product->name}, Current Stock: {$product->stock}, Qty Ordered: {$item['quantity']}");
                    
                    // Check if there's enough stock
                    if ($product->stock < $item['quantity']) {
                        $stockUpdateErrors[] = "Not enough stock for {$product->name}. Available: {$product->stock}, Requested: {$item['quantity']}";
                        continue;
                    }
                    
                    // Ensure stock doesn't go below 0
                    $newStock = max(0, $product->stock - $item['quantity']);
                    $product->stock = $newStock;
                    
                    // Update product status based on new stock level
                    if ($product->stock == 0) {
                        $product->status = 'Out of Stock';
                    } elseif ($product->stock <= 15) {
                        $product->status = 'Low Stock';
                    } else {
                        $product->status = 'In Stock';
                    }
                    
                    $product->save();
                    
                    // Log product after stock update
                    \Log::info("Product after stock update - ID: {$product->id}, Name: {$product->name}, New Stock: {$product->stock}, New Status: {$product->status}");
                }
            }
            
            // If there were any stock update errors, throw an exception
            if (!empty($stockUpdateErrors)) {
                throw new \Exception(implode("; ", $stockUpdateErrors));
            }

            // Generate invoice for this order
            $this->invoiceController->storeProductInvoice(Auth::id(), $invoiceItems, $totalAmount);

            // Clear cart from database if user is logged in
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                // Remove only the ordered items from the cart
                $remainingItems = collect($cart->items)->filter(function($cartItem) use ($validated) {
                    // Check if this cart item was not in the ordered items
                    return !collect($validated['items'])->contains('id', $cartItem['id']);
                })->values()->all();
                
                // Update or delete the cart
                if (count($remainingItems) > 0) {
                    $cart->update(['items' => $remainingItems]);
                } else {
                    $cart->delete();
                }
            }
            
            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => $order,
            ]);
            
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollBack();
            
            \Log::error('Order placement failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        $order = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('order-details', ['order' => $order]);
    }

    /**
     * Cancel an order (only if it's pending)
     */
    public function cancel($id)
    {
        // Find the order first with its items and products
        $order = Order::with('items.product')->where('user_id', Auth::id())->findOrFail($id);
        
        // Check if the order can be canceled (only if it's pending)
        if ($order->status !== 'Pending') {
            return redirect()->back()->with('error', 'Only pending orders can be cancelled.');
        }

        // Restore product stock for all items in the order
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product) {
                // Increase the stock
                $product->stock += $item->quantity;
                
                // Update product status based on new stock level
                if ($product->stock == 0) {
                    $product->status = 'Out of Stock';
                } elseif ($product->stock <= 15) {
                    $product->status = 'Low Stock';
                } else {
                    $product->status = 'In Stock';
                }
                
                $product->save();
            }
        }

        // Update the order status to Cancelled
        $order->update(['status' => 'Cancelled']);

        return redirect()->back()->with('success', 'Order has been cancelled successfully.');
    }
}