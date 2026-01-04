<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
             <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                    <div class="mb-4">
                         <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-3xl font-bold mb-2">Product Created Successfully!</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 text-lg">The product <strong class="text-gray-900 dark:text-white">{{ $product->name }}</strong> has been added to the inventory.</p>

                    <h3 class="text-xl font-semibold mb-4 text-left border-b pb-2 border-gray-200 dark:border-gray-700">Generated Variants</h3>
                    
                    <div class="relative overflow-x-auto rounded-lg mb-8 border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Unit</th>
                                    <th scope="col" class="px-6 py-3">SKU</th>
                                    <th scope="col" class="px-6 py-3">Price</th>
                                    <th scope="col" class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 last:border-b-0">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $variant->unit_value ? $variant->unit_value . ' ' : '' }}{{ $variant->unit->name }} ({{ $variant->unit->short_name }})</td>
                                        <td class="px-6 py-4 font-mono">{{ $variant->sku }}</td>
                                        <td class="px-6 py-4">Rs. {{ number_format($variant->selling_price, 2) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <button onclick="window.open('{{ route('products.barcode.print', $variant->id) }}', '_blank', 'width=400,height=400')" class="inline-flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h-4v-4H8m13-4V4H3v7m2-7v11h14V4.9M3 17h18m-8-5v-2m-3 2v-2"></path></svg>
                                                Print Barcode
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="{{ route('products.index') }}" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-colors">
                            Back to Product List
                        </a>
                        <a href="{{ route('products.create') }}" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 transition-colors shadow-lg shadow-green-500/30">
                            Add Another Product
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
