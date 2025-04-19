<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get the current user's cart
     */
    public function getCart()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated']);
        }
        
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart) {
            return response()->json(['success' => true, 'items' => []]);
        }
        
        return response()->json(['success' => true, 'items' => $cart->items]);
    }
    
    /**
     * Sync the cart with the server
     */
    public function syncCart(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated']);
        }
        
        $items = $request->input('items', []);
        
        // Find or create the cart
        $cart = Cart::updateOrCreate(
            ['user_id' => $user->id],
            ['items' => $items]
        );
        
        return response()->json(['success' => true, 'message' => 'Cart synced successfully']);
    }
    
    /**
     * Show the cart view
     */
    public function showCart()
    {
        return view('cart');
    }
} 