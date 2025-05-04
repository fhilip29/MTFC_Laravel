<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
{
    // Fetch the top 8 most purchased products, ordered by total purchases
    $topRatedProducts = Product::withCount('orderItems') // Count the associated order items for each product
        ->orderByDesc('order_items_count') // Order by the total number of order items (i.e., total purchases)
        ->take(8) // Get top 8 products
        ->get();

    // Ensure we have the correct image URL for the blade view
    $topRatedProducts->each(function ($product) {
        $product->imgUrl = asset('storage/' . $product->image); // Assuming images are stored in storage
    });

    // Chunk the products into rows of 4 for the carousel
    $chunks = $topRatedProducts->chunk(4);

    return view('home', compact('topRatedProducts', 'chunks'));
}
 
}


