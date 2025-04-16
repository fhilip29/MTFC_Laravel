@extends('layouts.admin')

@section('title', 'Product Management')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <div class="bg-[#1F2937] p-4 sm:p-6 rounded-2xl shadow-md border border-[#374151]">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-6 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2 w-full sm:w-auto">
                <i class="fas fa-box text-[#9CA3AF]"></i> Product Management
            </h1>
            <button class="bg-[#374151] hover:bg-[#4B5563] text-white font-semibold flex items-center gap-2 px-4 py-2 rounded-lg shadow transition-colors w-full sm:w-auto justify-center">
                <i class="fas fa-plus"></i> <span class="sm:inline">Add Product</span>
            </button>
        </div>

        <div class="mb-6">
            <div class="relative w-full sm:w-1/3">
                <input 
                    type="text" 
                    placeholder="Search products..." 
                    class="w-full pl-10 pr-4 py-2 bg-[#374151] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] text-sm sm:text-base"
                >
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]"></i>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-sm -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
            <table class="min-w-full text-xs sm:text-sm table-auto">
                <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-xs">
                    <tr>
                        <th class="py-4 px-4 text-left">Image</th>
                        <th class="py-4 px-4 text-left">Product Name</th>
                        <th class="py-4 px-4 text-left">Category</th>
                        <th class="py-4 px-4 text-left">Status</th>
                        <th class="py-4 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-[#9CA3AF]">
                    @php
                        $products = [
                            [
                                'name' => 'Protein Powder',
                                'category' => 'Supplements',
                                'image' => 'https://i.imgur.com/7Gzj4ZP.png',
                                'status' => 'In Stock',
                                'status_class' => 'bg-green-500',
                            ],
                            [
                                'name' => 'Weightlifting Belt',
                                'category' => 'Accessories',
                                'image' => 'https://i.imgur.com/Qfhx6vL.png',
                                'status' => 'Low Stock',
                                'status_class' => 'bg-orange-500',
                            ],
                            [
                                'name' => 'Pre-Workout',
                                'category' => 'Supplements',
                                'image' => 'https://i.imgur.com/pzSK7yT.png',
                                'status' => 'Out of Stock',
                                'status_class' => 'bg-red-500',
                            ],
                        ];
                    @endphp

                    @foreach($products as $product)
                        <tr class="hover:bg-[#374151] border-b border-[#374151]">
                            <td class="py-4 px-4 align-middle">
                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-16 h-16 object-contain rounded-md">
                            </td>
                            <td class="py-4 px-4 text-white align-middle">{{ $product['name'] }}</td>
                            <td class="py-4 px-4 align-middle">{{ $product['category'] }}</td>
                            <td class="py-4 px-4 align-middle">
                                <span class="text-white px-2 py-1 rounded text-xs {{ $product['status_class'] }}">
                                    {{ $product['status'] }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center align-middle">
                                <div class="flex justify-center gap-2">
                                    <a href="#" class="text-blue-400 hover:text-blue-300 cursor-pointer" title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="text-blue-400 hover:text-blue-300 cursor-pointer" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="text-blue-400 hover:text-blue-300 cursor-pointer" title="Delete Product">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Frontend-only confirmation dialog
function confirmDelete() {
    if (confirm('Are you sure you want to delete this product?')) {
        alert('Product deleted successfully!');
    }
}

// Add click handlers to all delete buttons
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('a:has(i.fa-trash)');
    deleteButtons.forEach(button => {
        button.addEventListener('click', confirmDelete);
    });
});
</script>
@endsection
