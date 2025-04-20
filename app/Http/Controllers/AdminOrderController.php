<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of all orders
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.orders.admin_orders', compact('orders'));
    }
    
    /**
     * Display the details of a specific order
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDetails($id)
    {
        try {
            $order = Order::with('items.product', 'user')
                ->findOrFail($id);
                
            return response()->json([
                'success' => true,
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }
    
    /**
     * Update the status of an order
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'status' => 'required|string|in:Pending,Accepted,Out for Delivery,Completed,Cancelled'
            ]);
            
            // Find the order with its items and products
            $order = Order::with('items.product')->findOrFail($id);
            
            // Get the previous status before updating
            $previousStatus = $order->status;
            $newStatus = $validated['status'];
            
            // Handle stock changes based on status transition
            if ($previousStatus !== $newStatus) {
                // If order was cancelled, restore the stock
                if ($newStatus === 'Cancelled' && $previousStatus !== 'Cancelled') {
                    $this->restoreProductStock($order);
                }
                
                // Update the status
                $order->status = $newStatus;
                $order->save();
            }
            
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'order' => $order
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Restore product stock when an order is cancelled
     *
     * @param Order $order
     * @return void
     */
    private function restoreProductStock($order)
    {
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
    }
} 