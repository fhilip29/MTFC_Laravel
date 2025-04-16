@extends('layouts.admin')

@section('title', 'Product Management')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-md">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">🛒 Product Management</h1>
    </div>

    <!-- Search and Add -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <input
            type="text"
            placeholder="Search Products"
            class="w-full md:w-1/3 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
        />

        <a href="#"
           class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border border-gray-200 text-sm rounded-xl overflow-hidden">
            <thead class="bg-gray-100 text-gray-700 text-left">
                <tr>
                    <th class="py-3 px-4">Image</th>
                    <th class="py-3 px-4">Product Name</th>
                    <th class="py-3 px-4">Description</th>
                    <th class="py-3 px-4">Price</th>
                    <th class="py-3 px-4">Stocks</th>
                    <th class="py-3 px-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-800">
                @php
                    $products = [
                        [
                            'image' => 'https://i.imgur.com/Qfhx6vL.png',
                            'name' => 'ON Amino Energy, 30 Servings',
                            'desc' => 'Sample description 2',
                            'price' => 600,
                            'stock' => 100,
                        ],
                        [
                            'image' => 'https://i.imgur.com/tMdX7la.png',
                            'name' => 'Scivation XTend, 30 Servings',
                            'desc' => 'Sample description 4',
                            'price' => 3000,
                            'stock' => 123,
                        ],
                        [
                            'image' => 'https://i.imgur.com/jkIFNyz.png',
                            'name' => 'Primeval Labs APESH*T Cutz, 50 Servings',
                            'desc' => 'Sample description 2',
                            'price' => 2500,
                            'stock' => 1,
                        ],
                        [
                            'image' => 'https://i.imgur.com/7Gzj4ZP.png',
                            'name' => 'All Max Classic All Whey, 5lbs',
                            'desc' => 'Sample description 2',
                            'price' => 3000,
                            'stock' => 100,
                        ],
                        [
                            'image' => 'https://i.imgur.com/pzSK7yT.png',
                            'name' => 'Nutrex Lipo-6 Black UC, 60 Capsules',
                            'desc' => 'Sample description',
                            'price' => 1000,
                            'stock' => 100,
                        ],
                    ];
                @endphp

                @foreach($products as $product)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                                 class="w-12 h-12 object-contain rounded">
                        </td>
                        <td class="py-3 px-4 font-medium">{{ $product['name'] }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $product['desc'] }}</td>
                        <td class="py-3 px-4 text-blue-600 font-semibold">₱{{ number_format($product['price']) }}</td>
                        <td class="py-3 px-4">{{ $product['stock'] }}</td>
                        <td class="py-3 px-4 text-center">
                            <a href="#" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-pen"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
