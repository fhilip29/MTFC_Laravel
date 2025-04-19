@extends('layouts.admin')

@section('title', 'Product Management')

<!-- Add CSRF Token meta tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <div class="bg-[#1F2937] p-4 sm:p-6 rounded-2xl shadow-md border border-[#374151]">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-6 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2 w-full sm:w-auto">
                <i class="fas fa-box text-[#9CA3AF]"></i> Product Management
            </h1>
            <button id="openAddProductModal" class="bg-[#374151] hover:bg-[#4B5563] text-white font-semibold flex items-center gap-2 px-4 py-2 rounded-lg shadow transition-colors w-full sm:w-auto justify-center">
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
                <tbody id="productsTableBody" class="text-[#9CA3AF]">
                    @forelse($products as $product)
                        <tr class="hover:bg-[#374151] border-b border-[#374151]">
                            <td class="py-4 px-4 align-middle">
                                @if($product->image)
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-contain rounded-md">
                                @else
                                    <div class="w-16 h-16 flex items-center justify-center bg-[#374151] rounded-md">
                                        <i class="fas fa-box text-[#9CA3AF] text-2xl"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-white align-middle">{{ $product->name }}</td>
                            <td class="py-4 px-4 align-middle">{{ $product->category }}</td>
                            <td class="py-4 px-4 align-middle">
                                @php
                                    $statusClass = 'bg-green-500';
                                    if($product->status == 'Low Stock') {
                                        $statusClass = 'bg-orange-500';
                                    } elseif($product->status == 'Out of Stock') {
                                        $statusClass = 'bg-red-500';
                                    }
                                @endphp
                                <span class="text-white px-2 py-1 rounded text-xs {{ $statusClass }}">
                                    {{ $product->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center align-middle">
                                <div class="flex justify-center gap-2">
                                    <a href="#" class="text-blue-400 hover:text-blue-300 cursor-pointer" title="Edit Product" onclick="openEditModal('{{ $product->id }}', '{{ $product->name }}', '{{ $product->category }}', '{{ $product->description }}', '{{ $product->price }}', '{{ $product->status }}', '{{ $product->image ? asset($product->image) : '' }}', '{{ $product->stock }}')">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="text-blue-400 hover:text-blue-300 cursor-pointer" title="View Details" onclick="openViewModal('{{ $product->name }}', '{{ $product->category }}', '{{ $product->description }}', '{{ $product->price }}', '{{ $product->status }}', '{{ $product->image ? asset($product->image) : '' }}', '{{ $product->stock }}')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="text-red-400 hover:text-red-300 cursor-pointer" title="Delete Product" onclick="confirmDelete('{{ $product->name }}', '{{ $product->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 px-4 text-center">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50" id="modalOverlay"></div>
        <div class="relative bg-[#1F2937] border border-[#374151] rounded-lg w-full max-w-lg mx-auto shadow-lg">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b border-[#374151]">
                <h3 class="text-xl font-semibold text-white">Add New Product</h3>
                <button id="closeAddProductModal" class="text-[#9CA3AF] hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6">
                <form id="addProductForm" action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="productName" class="block text-sm font-medium text-[#9CA3AF] mb-1">Product Name</label>
                        <input type="text" id="productName" name="name" class="w-full py-2 px-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]" required>
                    </div>
                    
                    <div>
                        <label for="productCategory" class="block text-sm font-medium text-[#9CA3AF] mb-1">Category</label>
                        <select id="productCategory" name="category" class="w-full py-2 px-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]" required>
                            <option value="" disabled selected>Select a category</option>
                            <option value="Merchandise">Merchandise</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Drinks">Drinks</option>
                            <option value="Supplements">Supplements</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="productDescription" class="block text-sm font-medium text-[#9CA3AF] mb-1">Description</label>
                        <textarea id="productDescription" name="description" rows="3" class="w-full py-2 px-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="productPrice" class="block text-sm font-medium text-[#9CA3AF] mb-1">Price</label>
                            <input type="number" id="productPrice" name="price" min="0" step="0.01" class="w-full py-2 px-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]" required>
                        </div>
                        
                        <div>
                            <label for="productStock" class="block text-sm font-medium text-[#9CA3AF] mb-1">Stock</label>
                            <input type="number" id="productStock" name="stock" min="0" step="1" class="w-full py-2 px-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]" required>
                            <p class="text-xs text-[#9CA3AF] mt-1">
                                0: Out of Stock | 1-15: Low Stock | 16+: In Stock
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <label for="productImage" class="block text-sm font-medium text-[#9CA3AF] mb-1">Product Image</label>
                        <div class="flex flex-col space-y-2">
                            <div class="flex items-center justify-center w-full">
                                <label for="productImage" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer bg-[#374151] border-[#4B5563] hover:bg-[#424B5D]">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6" id="uploadPlaceholder">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-[#9CA3AF] mb-2"></i>
                                        <p class="mb-2 text-sm text-[#9CA3AF]">Click to upload or drag and drop</p>
                                        <p class="text-xs text-[#9CA3AF]">PNG, JPG or JPEG (MAX. 2MB)</p>
                                    </div>
                                    <div id="imagePreviewContainer" class="hidden w-full h-full flex items-center justify-center">
                                        <img id="imagePreview" class="max-h-28 max-w-full object-contain" src="#" alt="Preview">
                                    </div>
                                    <input id="productImage" name="image" type="file" accept="image/png, image/jpeg, image/jpg" class="hidden" />
                                </label>
                            </div>
                            <span id="selectedFileName" class="text-xs text-[#9CA3AF]"></span>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-[#374151] flex justify-end">
                        <button type="button" id="cancelAddProduct" class="px-4 py-2 text-[#9CA3AF] border border-[#4B5563] rounded-md hover:bg-[#374151] mr-2">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Add Product
                        </button>
                    </div>
                </form>
            </div>
            <!-- Modal footer -->
            <div class="px-6 py-4 border-t border-[#374151] hidden">
                <button id="cancelAddProduct2" class="px-4 py-2 text-[#9CA3AF] border border-[#4B5563] rounded-md hover:bg-[#374151] mr-2">
                    Cancel
                </button>
                <button id="submitAddProduct" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Add Product
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Product Modal -->
<div id="viewProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50" id="viewModalOverlay"></div>
        <div class="relative bg-[#1F2937] border border-[#374151] rounded-lg w-full max-w-lg mx-auto shadow-lg">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b border-[#374151]">
                <h3 class="text-xl font-semibold text-white">Product Details</h3>
                <button id="closeViewProductModal" class="text-[#9CA3AF] hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6">
                <div class="flex flex-col items-center mb-6">
                    <div id="viewProductImage" class="w-40 h-40 bg-[#374151] rounded-md flex items-center justify-center mb-4 overflow-hidden">
                        <img id="viewImageSrc" class="max-h-40 max-w-40 object-contain" src="" alt="Product image">
                        <i id="viewImagePlaceholder" class="fas fa-box text-[#9CA3AF] text-4xl hidden"></i>
                    </div>
                    <h3 id="viewProductName" class="text-xl font-semibold text-white"></h3>
                </div>
                
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <span class="text-sm text-[#9CA3AF]">Category:</span>
                        <p id="viewProductCategory" class="text-white"></p>
                    </div>
                    
                    <div>
                        <span class="text-sm text-[#9CA3AF]">Price:</span>
                        <p id="viewProductPrice" class="text-white"></p>
                    </div>
                    
                    <div>
                        <span class="text-sm text-[#9CA3AF]">Stock:</span>
                        <p id="viewProductStock" class="text-white"></p>
                    </div>
                    
                    <div>
                        <span class="text-sm text-[#9CA3AF]">Status:</span>
                        <p id="viewProductStatusContainer">
                            <span id="viewProductStatus" class="inline-block text-white px-2 py-1 rounded text-xs mt-1"></span>
                        </p>
                    </div>
                    
                    <div>
                        <span class="text-sm text-[#9CA3AF]">Description:</span>
                        <p id="viewProductDescription" class="text-white"></p>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="px-6 py-4 border-t border-[#374151] flex justify-end">
                <button id="closeViewProduct" class="px-4 py-2 bg-[#374151] text-white rounded-md hover:bg-[#4B5563]">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="editProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50" id="editModalOverlay"></div>
        <div class="relative bg-[#1F2937] border border-[#374151] rounded-lg w-full max-w-lg mx-auto shadow-lg">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b border-[#374151]">
                <h3 class="text-xl font-semibold text-white">Edit Product</h3>
                <button id="closeEditProductModal" class="text-[#9CA3AF] hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6">
                <form id="editProductForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editProductId" name="product_id">
                    
                    <div>
                        <label for="editProductName" class="block text-sm font-medium text-[#9CA3AF] mb-1">Product Name</label>
                        <input type="text" id="editProductName" name="name" class="w-full py-2 px-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]" required>
                    </div>
                    
                    <div>
                        <label for="editProductCategory" class="block text-sm font-medium text-[#9CA3AF] mb-1">Category</label>
                        <select id="editProductCategory" name="category" class="w-full py-2 px-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]" required>
                            <option value="Merchandise">Merchandise</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Drinks">Drinks</option>
                            <option value="Supplements">Supplements</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="editProductDescription" class="block text-sm font-medium text-[#9CA3AF] mb-1">Description</label>
                        <textarea id="editProductDescription" name="description" rows="3" class="w-full py-2 px-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="editProductPrice" class="block text-sm font-medium text-[#9CA3AF] mb-1">Price</label>
                            <input type="number" id="editProductPrice" name="price" min="0" step="0.01" class="w-full py-2 px-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]" required>
                        </div>
                        <div>
                            <label for="editProductStock" class="block text-sm font-medium text-[#9CA3AF] mb-1">Stock</label>
                            <input type="number" id="editProductStock" name="stock" min="0" step="1" class="w-full py-2 px-3 bg-[#374151] border border-[#4B5563] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]" required>
                            <p class="text-xs text-[#9CA3AF] mt-1">
                                0: Out of Stock | 1-15: Low Stock | 16+: In Stock
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Current Image</label>
                        <div id="editCurrentImageContainer" class="w-full h-40 border border-[#4B5563] rounded-md flex items-center justify-center mb-2">
                            <img id="editCurrentImage" class="max-h-full max-w-full object-contain rounded-md" src="" alt="Current product image">
                            <div id="editNoImagePlaceholder" class="flex items-center justify-center w-40 h-40 bg-[#374151] rounded-md hidden">
                                <i class="fas fa-box text-[#9CA3AF] text-4xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="editProductImage" class="block text-sm font-medium text-[#9CA3AF] mb-1">Update Image (optional)</label>
                        <div class="flex flex-col space-y-2">
                            <div class="flex items-center justify-center w-full">
                                <label for="editProductImage" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer bg-[#374151] border-[#4B5563] hover:bg-[#424B5D]">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6" id="editUploadPlaceholder">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-[#9CA3AF] mb-2"></i>
                                        <p class="mb-2 text-sm text-[#9CA3AF]">Click to upload or drag and drop</p>
                                        <p class="text-xs text-[#9CA3AF]">PNG, JPG or JPEG (MAX. 2MB)</p>
                                    </div>
                                    <div id="editImagePreviewContainer" class="hidden w-full h-full flex items-center justify-center">
                                        <img id="editImagePreview" class="max-h-28 max-w-full object-contain" src="#" alt="Preview">
                                    </div>
                                    <input id="editProductImage" name="image" type="file" accept="image/png, image/jpeg, image/jpg" class="hidden" />
                                </label>
                            </div>
                            <span id="editSelectedFileName" class="text-xs text-[#9CA3AF]"></span>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-[#374151] flex justify-end">
                        <button type="button" id="cancelEditProduct" class="px-4 py-2 text-[#9CA3AF] border border-[#4B5563] rounded-md hover:bg-[#374151] mr-2">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Delete confirmation with SweetAlert
function confirmDelete(productName, productId) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete "${productName}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        background: '#1F2937',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form element for DELETE request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/products/${productId}`;
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            // Add method spoofing for DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Add form to body, submit it, and remove it
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Modal control
document.addEventListener('DOMContentLoaded', function() {
    // Modal elements
    const addProductModal = document.getElementById('addProductModal');
    const openAddProductBtn = document.getElementById('openAddProductModal');
    const closeAddProductBtn = document.getElementById('closeAddProductModal');
    const cancelAddProductBtn = document.getElementById('cancelAddProduct');
    const submitAddProductBtn = document.getElementById('submitAddProduct');
    const modalOverlay = document.getElementById('modalOverlay');
    const productForm = document.getElementById('addProductForm');
    const productsTableBody = document.getElementById('productsTableBody');
    
    // Image upload elements
    const productImageInput = document.getElementById('productImage');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const selectedFileName = document.getElementById('selectedFileName');
    
    // Handle image upload
    productImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // File size validation (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    title: 'File Too Large',
                    text: 'File size should not exceed 2MB',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#fff'
                });
                productImageInput.value = '';
                return;
            }
            
            // Show file name
            selectedFileName.textContent = file.name;
            
            // Show image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                uploadPlaceholder.classList.add('hidden');
                imagePreviewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            // Reset preview
            selectedFileName.textContent = '';
            uploadPlaceholder.classList.remove('hidden');
            imagePreviewContainer.classList.add('hidden');
        }
    });
    
    // Open modal
    openAddProductBtn.addEventListener('click', function() {
        addProductModal.classList.remove('hidden');
    });
    
    // Close modal functions
    function closeModal() {
        addProductModal.classList.add('hidden');
        productForm.reset();
        uploadPlaceholder.classList.remove('hidden');
        imagePreviewContainer.classList.add('hidden');
        selectedFileName.textContent = '';
    }
    
    closeAddProductBtn.addEventListener('click', closeModal);
    cancelAddProductBtn.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', closeModal);
    
    // Display success message if it exists in the session
    @if(session('success'))
    Swal.fire({
        title: 'Success!',
        text: "{{ session('success') }}",
        icon: 'success',
        timer: 2000,
        showConfirmButton: false,
        background: '#1F2937',
        color: '#fff'
    });
    @endif
    
    // Display any validation errors
    @if($errors->any())
    Swal.fire({
        title: 'Form Error',
        html: `@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach`,
        icon: 'error',
        background: '#1F2937',
        color: '#fff'
    });
    @endif
});

// View Product Modal Functions
function openViewModal(name, category, description, price, status, image, stock = 0) {
    // Set product details
    document.getElementById('viewProductName').textContent = name;
    document.getElementById('viewProductCategory').textContent = category;
    document.getElementById('viewProductPrice').textContent = `$${parseFloat(price).toFixed(2)}`;
    document.getElementById('viewProductStock').textContent = stock || 'N/A';
    document.getElementById('viewProductDescription').textContent = description || 'No description available';
    
    // Set status with appropriate class
    const statusElement = document.getElementById('viewProductStatus');
    statusElement.textContent = status;
    statusElement.className = 'inline-block text-white px-2 py-1 rounded text-xs mt-1';
    
    if (status === 'In Stock') {
        statusElement.classList.add('bg-green-500');
    } else if (status === 'Low Stock') {
        statusElement.classList.add('bg-orange-500');
    } else if (status === 'Out of Stock') {
        statusElement.classList.add('bg-red-500');
    }
    
    // Set image
    const imageSrc = document.getElementById('viewImageSrc');
    const imagePlaceholder = document.getElementById('viewImagePlaceholder');
    
    if (image) {
        imageSrc.src = image;
        imageSrc.classList.remove('hidden');
        imagePlaceholder.classList.add('hidden');
    } else {
        imageSrc.classList.add('hidden');
        imagePlaceholder.classList.remove('hidden');
    }
    
    // Show modal
    document.getElementById('viewProductModal').classList.remove('hidden');
}

// Edit Product Modal Functions
function openEditModal(id, name, category, description, price, status, image, stock = 0) {
    // Set form action
    document.getElementById('editProductForm').action = `/admin/products/${id}`;
    
    // Set form values
    document.getElementById('editProductId').value = id;
    document.getElementById('editProductName').value = name;
    document.getElementById('editProductCategory').value = category;
    document.getElementById('editProductDescription').value = description || '';
    document.getElementById('editProductPrice').value = price;
    document.getElementById('editProductStock').value = stock || 0;
    
    // Set current image
    const currentImage = document.getElementById('editCurrentImage');
    const noImagePlaceholder = document.getElementById('editNoImagePlaceholder');
    
    if (image) {
        currentImage.src = image;
        currentImage.classList.remove('hidden');
        noImagePlaceholder.classList.add('hidden');
    } else {
        currentImage.classList.add('hidden');
        noImagePlaceholder.classList.remove('hidden');
    }
    
    // Reset new image upload
    document.getElementById('editProductImage').value = '';
    document.getElementById('editSelectedFileName').textContent = '';
    document.getElementById('editUploadPlaceholder').classList.remove('hidden');
    document.getElementById('editImagePreviewContainer').classList.add('hidden');
    
    // Show modal
    document.getElementById('editProductModal').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    // ... existing code ...
    
    // View modal elements
    const viewProductModal = document.getElementById('viewProductModal');
    const closeViewProductBtn = document.getElementById('closeViewProductModal');
    const closeViewProductButtonFooter = document.getElementById('closeViewProduct');
    const viewModalOverlay = document.getElementById('viewModalOverlay');
    
    // Edit modal elements
    const editProductModal = document.getElementById('editProductModal');
    const closeEditProductBtn = document.getElementById('closeEditProductModal');
    const cancelEditProductBtn = document.getElementById('cancelEditProduct');
    const editModalOverlay = document.getElementById('editModalOverlay');
    const editProductForm = document.getElementById('editProductForm');
    
    // Edit image upload elements
    const editProductImageInput = document.getElementById('editProductImage');
    const editImagePreview = document.getElementById('editImagePreview');
    const editImagePreviewContainer = document.getElementById('editImagePreviewContainer');
    const editUploadPlaceholder = document.getElementById('editUploadPlaceholder');
    const editSelectedFileName = document.getElementById('editSelectedFileName');
    
    // Handle edit image upload
    editProductImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // File size validation (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    title: 'File Too Large',
                    text: 'File size should not exceed 2MB',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#fff'
                });
                editProductImageInput.value = '';
                return;
            }
            
            // Show file name
            editSelectedFileName.textContent = file.name;
            
            // Show image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                editImagePreview.src = e.target.result;
                editUploadPlaceholder.classList.add('hidden');
                editImagePreviewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            // Reset preview
            editSelectedFileName.textContent = '';
            editUploadPlaceholder.classList.remove('hidden');
            editImagePreviewContainer.classList.add('hidden');
        }
    });
    
    // Close view modal functions
    function closeViewModal() {
        viewProductModal.classList.add('hidden');
    }
    
    closeViewProductBtn.addEventListener('click', closeViewModal);
    closeViewProductButtonFooter.addEventListener('click', closeViewModal);
    viewModalOverlay.addEventListener('click', closeViewModal);
    
    // Close edit modal functions
    function closeEditModal() {
        editProductModal.classList.add('hidden');
        editProductForm.reset();
        editUploadPlaceholder.classList.remove('hidden');
        editImagePreviewContainer.classList.add('hidden');
        editSelectedFileName.textContent = '';
    }
    
    closeEditProductBtn.addEventListener('click', closeEditModal);
    cancelEditProductBtn.addEventListener('click', closeEditModal);
    editModalOverlay.addEventListener('click', closeEditModal);
});
</script>
@endsection
