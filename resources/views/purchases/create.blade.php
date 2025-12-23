<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Purchase Order') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="purchaseForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('purchases.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Select Supplier</label>
                            <select name="supplier_id" required class="shadow appearance-none border rounded w-full md:w-1/2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Choose Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->business_name }} ({{ $supplier->name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <hr class="mb-6">

                        <h3 class="text-lg font-semibold mb-4">Items</h3>
                        <div class="border rounded p-4 bg-gray-50 mb-6">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex flex-wrap gap-4 mb-4 items-end border-b pb-4 last:border-b-0">
                                    <div class="w-full md:w-1/3">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Product</label>
                                        <select :name="'items['+index+'][product_id]'" x-model="item.product_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-full md:w-1/4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Quantity</label>
                                        <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" min="1" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>
                                    <div class="w-full md:w-1/4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Purchasing Price</label>
                                        <input type="number" :name="'items['+index+'][purchasing_price]'" x-model="item.price" min="0" step="0.01" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>
                                    <div class="w-full md:w-auto pt-2">
                                        <button type="button" @click="removeItem(index)" class="text-red-600 font-bold hover:underline" x-show="items.length > 1">Remove</button>
                                    </div>
                                </div>
                            </template>
                            <button type="button" @click="addItem()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded mt-2">
                                + Add Another Item
                            </button>
                        </div>
                        
                        <div class="mb-6 text-right">
                            <h3 class="text-xl font-bold">Total Request Value: <span x-text="grandTotal()"></span></h3>
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                                Create Purchase Order
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function purchaseForm() {
            return {
                items: [{ product_id: '', quantity: 1, price: 0 }],
                addItem() {
                    this.items.push({ product_id: '', quantity: 1, price: 0 });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                grandTotal() {
                    return this.items.reduce((total, item) => total + (item.price * item.quantity), 0).toFixed(2);
                }
            }
        }
    </script>
</x-app-layout>
