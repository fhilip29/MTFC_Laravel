<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.product.admin_product', compact('products'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_promo' => 'nullable|boolean',
            'original_price' => 'nullable|numeric|min:0|required_if:is_promo,1',
            'promo_ends_at' => 'nullable|date',
        ]);
        
        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->category = $request->category;
        $product->price = $request->price;
        $product->stock = $request->stock;
        
        // Set status based on stock
        if ($product->stock == 0) {
            $product->status = 'Out of Stock';
        } elseif ($product->stock <= 15) {
            $product->status = 'Low Stock';
        } else {
            $product->status = 'In Stock';
        }
        
        // Handle promo fields
        $product->is_promo = $request->boolean('is_promo', false);
        if ($product->is_promo) {
            $product->original_price = $request->original_price;
            $product->promo_ends_at = $request->promo_ends_at;
        }
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);
            $product->image = 'images/products/' . $imageName;
        }
        
        $product->save();
        
        return redirect()->route('admin.product.products')->with('success', 'Product added successfully!');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_promo' => 'nullable|boolean',
            'original_price' => 'nullable|numeric|min:0|required_if:is_promo,1',
            'promo_ends_at' => 'nullable|date',
        ]);
        
        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->description = $request->description;
        $product->category = $request->category;
        $product->price = $request->price;
        $product->stock = $request->stock;
        
        // Set status based on stock
        if ($product->stock == 0) {
            $product->status = 'Out of Stock';
        } elseif ($product->stock <= 15) {
            $product->status = 'Low Stock';
        } else {
            $product->status = 'In Stock';
        }
        
        // Handle promo fields
        $product->is_promo = $request->boolean('is_promo', false);
        if ($product->is_promo) {
            $product->original_price = $request->original_price;
            $product->promo_ends_at = $request->promo_ends_at;
        } else {
            $product->original_price = null;
            $product->promo_ends_at = null;
        }
        
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }
            
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);
            $product->image = 'images/products/' . $imageName;
        }
        
        $product->save();
        
        return redirect()->route('admin.product.products')->with('success', 'Product updated successfully!');
    }
    
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Delete image if it exists
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }
        
        $product->delete();
        
        return redirect()->route('admin.product.products')->with('success', 'Product deleted successfully!');
    }
}
