<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        // Get products grouped by category
        $merchandise = Product::where('category', 'Merchandise')->get();
        $equipment = Product::where('category', 'Equipment')->get();
        $drinks = Product::where('category', 'Drinks')->get();
        $supplements = Product::where('category', 'Supplements')->get();
        
        // Combine drinks and supplements into one category for display
        $drinksAndSupplements = $drinks->concat($supplements);
        
        return view('shop', compact('merchandise', 'equipment', 'drinksAndSupplements'));
    }
    
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('product.show', compact('product'));
    }
} 