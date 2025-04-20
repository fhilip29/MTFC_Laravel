<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
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

        // Create order items
        foreach ($validated['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            // Update product stock (reserving stock when order is placed)
            $product = Product::find($item['id']);
            if ($product) {
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
            }
        }

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

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'order' => $order,
        ]);
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