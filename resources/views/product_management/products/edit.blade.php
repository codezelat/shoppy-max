<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Basic Info -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Product Name</label>
                                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">SKU / Barcode Data</label>
                                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                                    <select name="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Sub Category</label>
                                    <select name="sub_category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="">Select Sub Category</option>
                                         @foreach($subCategories as $subCategory)
                                            <option value="{{ $subCategory->id }}" {{ $product->sub_category_id == $subCategory->id ? 'selected' : '' }}>
                                                {{ $subCategory->name }} ({{ $subCategory->category->name ?? 'No Parent' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Unit</label>
                                    <select name="unit_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="">Select Unit</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ $product->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->short_name }})</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <!-- Pricing & Inventory -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing & Inventory</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Selling Price</label>
                                    <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Limit Price (Min)</label>
                                    <input type="number" step="0.01" name="limit_price" value="{{ old('limit_price', $product->limit_price) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Alert Quantity</label>
                                    <input type="number" name="alert_quantity" value="{{ old('alert_quantity', $product->alert_quantity) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>

                                <div class="mb-4 bg-gray-50 p-4 rounded border">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Current Stock</label>
                                    <!-- Display only, cannot adjust manually -->
                                    <input type="text" value="{{ $product->quantity }}" disabled class="bg-gray-100 cursor-not-allowed shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <p class="text-xs text-red-500 mt-1">To adjust stock, please add a purchase entry.</p>
                                </div>

                                 <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                                    <textarea name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="3">{{ old('description', $product->description) }}</textarea>
                                </div>

                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
