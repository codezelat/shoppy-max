<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Reseller Order') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="orderForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_type" value="reseller">

                        <!-- Reseller & Customer Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Select Reseller</label>
                                <select name="reseller_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Choose Reseller</option>
                                    @foreach($resellers as $reseller)
                                        <option value="{{ $reseller->id }}">{{ $reseller->name }} ({{ $reseller->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Customer Info (Same as general but often mandatory for delivery) -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Customer Name</label>
                                <input type="text" name="customer_name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
                                <input type="text" name="customer_phone" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                                <textarea name="customer_address" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-24"></textarea>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">City</label>
                                <select name="city_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Select City</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="mb-6">

                        <!-- Products Section (Exact duplicate of general order form) -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Products</h3>
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex flex-wrap gap-4 mb-4 items-end bg-gray-50 p-4 rounded">
                                    <div class="w-full md:w-1/3">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Product</label>
                                        <select :name="'products['+index+'][id]'" x-model="item.id" @change="updatePrice(index)" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">{{ $product->name }} ({{ $product->sku }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-full md:w-1/4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Quantity</label>
                                        <input type="number" :name="'products['+index+'][quantity]'" x-model="item.quantity" min="1" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>
                                    <div class="w-full md:w-1/4 pt-2">
                                        <p class="text-sm font-bold">Total: <span x-text="(item.price * item.quantity).toFixed(2)"></span></p>
                                    </div>
                                    <button type="button" @click="removeItem(index)" class="bg-red-500 text-white px-3 py-1 rounded" x-show="items.length > 1">Remove</button>
                                </div>
                            </template>
                            <button type="button" @click="addItem()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Add Product</button>
                        </div>
                        
                        <div class="mb-6 text-right">
                            <h3 class="text-xl font-bold">Grand Total: <span x-text="grandTotal()"></span></h3>
                        </div>

                        <!-- Payment & Notes -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Payment Method</label>
                                <select name="payment_method" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="cod">Cash on Delivery</option>
                                    <option value="online">Online Payment</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Sales Note</label>
                                <input type="text" name="sales_note" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Create Reseller Order
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Reuse the same script as create.blade.php -->
    <script>
        function orderForm() {
            return {
                items: [{ id: '', quantity: 1, price: 0 }],
                addItem() {
                    this.items.push({ id: '', quantity: 1, price: 0 });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                updatePrice(index) {
                    const select = document.querySelector(`select[name='products[${index}][id]']`);
                    if (select && select.selectedOptions[0]) {
                        this.items[index].price = parseFloat(select.selectedOptions[0].getAttribute('data-price')) || 0;
                    }
                },
                grandTotal() {
                    return this.items.reduce((total, item) => total + (item.price * item.quantity), 0).toFixed(2);
                }
            }
        }
    </script>
</x-app-layout>
