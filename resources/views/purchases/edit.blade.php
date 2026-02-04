<x-app-layout>
    <!-- Header & Breadcrumb -->
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Edit Purchase') }}
            </h2>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                     <li>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <a href="{{ route('purchases.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Purchases</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Edit</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('purchases.update', $purchase) }}" x-data="purchaseForm()" @submit.prevent="submitForm">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
            
            <!-- Left Column: Details & Items -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Basic Info Card -->
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">Purchase Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Supplier -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Supplier <span class="text-red-500">*</span></label>
                            <select name="supplier_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->business_name ?? $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Date -->
                        <div>
                             <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date <span class="text-red-500">*</span></label>
                             <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                      <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                    </svg>
                                </div>
                                <input type="date" name="purchase_date" value="{{ $purchase->purchase_date->format('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                            </div>
                        </div>

                        <!-- Reference -->
                         <div>
                             <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reference No <span class="text-red-500">*</span></label>
                             <input type="text" name="purchase_number" value="{{ $purchase->purchase_number }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        </div>
                    </div>
                </div>

                <!-- Product Items -->
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-4 border-b pb-2 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Items</h3>
                        <button type="button" @click="addItem()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Item
                        </button>
                    </div>

                    <div class="relative overflow-x-auto lg:overflow-visible">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3 min-w-[250px]">Product / Description</th>
                                    <th class="px-4 py-3 w-24">Qty</th>
                                    <th class="px-4 py-3 w-32">Unit Price</th>
                                    <th class="px-4 py-3 w-32 text-right">Total</th>
                                    <th class="px-4 py-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <!-- Product Search -->
                                        <td class="px-4 py-3 align-top relative">
                                            <div class="relative" x-data="{ search: '', open: false, results: [] }">
                                                <input type="text" 
                                                       x-model="item.product_name" 
                                                       @input.debounce.300ms="
                                                            search = item.product_name;
                                                            if(search.length > 1) {
                                                                fetch(`{{ route('orders.search-products') }}?query=${search}`)
                                                                    .then(res => res.json())
                                                                    .then(data => { results = data; open = true; });
                                                            } else { open = false; }
                                                       "
                                                       @focus="if(item.product_name.length > 1) open = true"
                                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                                                       placeholder="Type product name..."
                                                       required
                                                       autocomplete="off"
                                                >
                                                
                                                <!-- Dropdown Results -->
                                                <div x-show="open" 
                                                     @click.outside="open = false" 
                                                     class="absolute z-50 w-[400px] bg-white rounded-lg shadow-2xl border border-gray-200 dark:border-gray-600 dark:bg-gray-800 mt-1 max-h-60 overflow-y-auto left-0 transform transition-all duration-200 origin-top-left">
                                                    
                                                    <div x-show="results.length === 0" class="p-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                                        No products found.
                                                    </div>

                                                    <ul x-show="results.length > 0" class="py-1">
                                                        <template x-for="res in results" :key="res.id">
                                                            <li @click="
                                                                item.product_id = res.id;
                                                                item.product_name = res.name;
                                                                open = false;
                                                            " class="px-4 py-3 hover:bg-blue-50 dark:hover:bg-gray-700 cursor-pointer text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 last:border-0 flex justify-between items-center group transition-colors">
                                                                
                                                                <div class="flex items-center gap-3">
                                                                    <div class="w-8 h-8 rounded bg-gray-100 dark:bg-gray-600 flex items-center justify-center text-gray-400">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                                    </div>
                                                                    <div class="flex flex-col">
                                                                        <span x-text="res.name" class="font-semibold text-sm"></span>
                                                                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="'SKU: ' + res.sku"></span>
                                                                    </div>
                                                                </div>
                                                                
                                                                <span class="text-xs font-mono bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                                                    Select
                                                                </span>
                                                            </li>
                                                        </template>
                                                    </ul>
                                                </div>
                                                <input type="hidden" :name="`items[${index}][product_id]`" x-model="item.product_id">
                                                <input type="hidden" :name="`items[${index}][product_name]`" x-model="item.product_name">
                                            </div>
                                        </td>
                                        
                                        <!-- Qty -->
                                        <td class="px-4 py-3 align-top">
                                            <input type="number" step="1" min="1" x-model="item.quantity" :name="`items[${index}][quantity]`" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white text-center" required>
                                        </td>
                                        
                                        <!-- Price -->
                                        <td class="px-4 py-3 align-top">
                                            <input type="number" step="0.01" min="0" x-model="item.purchase_price" :name="`items[${index}][purchase_price]`" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white text-right" required>
                                        </td>
                                        
                                        <!-- Total -->
                                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white align-middle">
                                            <span x-text="(item.quantity * item.purchase_price).toFixed(2)"></span>
                                        </td>
                                        
                                        <!-- Remove -->
                                        <td class="px-4 py-3 align-middle text-center">
                                            <button type="button" @click="removeItem(index)" class="text-gray-400 hover:text-red-500 transition-colors p-1 hover:bg-red-50 rounded-full dark:hover:bg-gray-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white">
                                    <td colspan="3" class="px-4 py-3 text-right">Total Items Cost:</td>
                                    <td class="px-4 py-3 text-right" x-text="subTotal.toFixed(2)"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">Payment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                             <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Amount Paid</label>
                             <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">Rs.</span>
                                </div>
                                <input type="number" step="0.01" name="paid_amount" x-model="paid_amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                             <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Payment Method</label>
                             <select name="payment_method" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                <option value="Cash" {{ $purchase->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Card" {{ $purchase->payment_method == 'Card' ? 'selected' : '' }}>Card</option>
                                <option value="Cheque" {{ $purchase->payment_method == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="Bank Transfer" {{ $purchase->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                             </select>
                        </div>
                         <div>
                             <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Payment Account</label>
                             <input type="text" name="payment_account" value="{{ $purchase->payment_account }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="e.g. Bank Acc No">
                        </div>
                        <div>
                             <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Note / Cheque No</label>
                             <input type="text" name="payment_note" value="{{ $purchase->payment_note }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- Right Column: Totals -->
            <div class="space-y-6">
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800 border border-gray-200 dark:border-gray-700 sticky top-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 border-b pb-2 dark:border-gray-700">Financial Summary</h3>
                    
                    <div class="flex justify-between mb-3 text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="'Rs. ' + subTotal.toFixed(2)"></span>
                        <input type="hidden" name="sub_total" :value="subTotal">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Discount</label>
                        <div class="flex gap-2">
                            <input type="number" x-model="discount_value" name="discount_value" placeholder="0" class="w-2/3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-right">
                            <select x-model="discount_type" name="discount_type" class="w-1/3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="fixed">Rs.</option>
                                <option value="percentage">%</option>
                            </select>
                        </div>
                        <p class="text-xs text-green-600 mt-1 text-right italic" x-show="discountAmount > 0">
                            - Rs. <span x-text="discountAmount.toFixed(2)"></span>
                        </p>
                        <input type="hidden" name="discount_amount" :value="discountAmount">
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 flex justify-between items-center mb-6">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Net Total</span>
                        <span class="text-xl font-extrabold text-blue-600" x-text="'Rs. ' + netTotal.toFixed(2)"></span>
                         <input type="hidden" name="net_total" :value="netTotal">
                    </div>
                    
                    <!-- Balance Indicator -->
                    <div class="mb-6 p-3 rounded-md text-sm font-medium flex justify-between items-center" 
                         :class="paid_amount >= netTotal ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        <span>Balance Due:</span>
                        <span x-text="'Rs. ' + Math.max(0, netTotal - paid_amount).toFixed(2)"></span>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 shadow-md">
                            Update Purchase
                        </button>
                        <a href="{{ route('purchases.index') }}" class="w-full text-center text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 shadow-sm">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        function purchaseForm() {
            return {
                items: @json($purchase->items->map(function($item){ return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'purchase_price' => $item->purchase_price
                ]; })),
                discount_type: '{{ $purchase->discount_type ?? 'fixed' }}',
                discount_value: {{ $purchase->discount_value ?? 0 }},
                paid_amount: {{ $purchase->paid_amount ?? 0 }},
                
                addItem() {
                    this.items.push({ product_id: null, product_name: '', quantity: 1, purchase_price: 0 });
                },
                
                removeItem(index) {
                    if(this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                
                get subTotal() {
                    return this.items.reduce((sum, item) => sum + (parseFloat(item.quantity || 0) * parseFloat(item.purchase_price || 0)), 0);
                },
                
                get discountAmount() {
                    if (this.discount_type === 'percentage') {
                        return (this.subTotal * parseFloat(this.discount_value || 0)) / 100;
                    }
                    return parseFloat(this.discount_value || 0);
                },
                
                get netTotal() {
                    let total = this.subTotal - this.discountAmount;
                    return total > 0 ? total : 0;
                },

                submitForm(e) {
                    if (this.items.some(item => !item.product_name || item.quantity <= 0)) {
                        alert('Please ensure all items have a product and valid quantity.');
                        return;
                    }
                    e.target.submit();
                }
            }
        }
    </script>
</x-app-layout>
