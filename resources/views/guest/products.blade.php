<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 px-4">Our Products</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 px-4">
                @forelse($products as $product)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-300">
                        <!-- Image Placeholder -->
                        <div class="h-48 bg-gray-200 w-full flex items-center justify-center text-gray-500">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            @else
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @endif
                        </div>
                        
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-500 mb-2">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            
                            <div class="flex justify-between items-center mt-4">
                                <span class="text-xl font-bold text-gray-900">${{ number_format($product->selling_price, 2) }}</span>
                                <span class="text-xs bg-gray-100 text-gray-800 py-1 px-2 rounded-full">
                                    {{ $product->quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 sm:col-span-2 lg:col-span-4 text-center py-10 text-gray-500">
                        No products available at the moment.
                    </div>
                @endforelse
            </div>

            <div class="mt-8 px-4">
                {{ $products->links() }}
            </div>
            
             <div class="mt-8 px-4 text-center">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-900">Back to Login</a>
            </div>
        </div>
    </div>
</x-guest-layout>
