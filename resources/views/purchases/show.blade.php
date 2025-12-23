<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Purchase #{{ $purchase->purchasing_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Status Alert -->
            <div class="mb-6 p-4 rounded-lg flex justify-between items-center {{ $purchase->status === 'verified' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                <div>
                    <span class="font-bold text-lg">Status: {{ ucfirst($purchase->status) }}</span>
                    @if($purchase->grn_number)
                        <p class="mt-1 font-mono">GRN: {{ $purchase->grn_number }}</p>
                    @endif
                </div>
                <div>
                    @if($purchase->status === 'pending')
                    <form action="{{ route('purchases.verify', $purchase->id) }}" method="POST" onsubmit="return confirm('Ensure all items are checked. Stock will be updated.');">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-800 text-white font-bold py-2 px-4 rounded shadow">
                            Verify & Generate GRN
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-gray-500 uppercase text-xs font-bold mb-2">Supplier</h3>
                            <p class="text-lg font-bold">{{ $purchase->supplier->business_name }}</p>
                            <p>{{ $purchase->supplier->name }} | {{ $purchase->supplier->mobile }}</p>
                        </div>
                        <div>
                            <h3 class="text-gray-500 uppercase text-xs font-bold mb-2">Order Details</h3>
                            <p>Created By: {{ $purchase->user->name ?? 'Unknown' }}</p>
                            <p>Date: {{ $purchase->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold mb-4">Items Received</h3>
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Product</th>
                                <th class="px-6 py-3 text-right">Unit Cost</th>
                                <th class="px-6 py-3 text-right">Quantity</th>
                                <th class="px-6 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($purchase->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <span class="font-bold">{{ $item->product->name }}</span>
                                    <br><span class="text-xs text-gray-500">{{ $item->product->sku }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">{{ number_format($item->purchasing_price, 2) }}</td>
                                <td class="px-6 py-4 text-right font-bold">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right">{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="bg-gray-100 font-bold">
                                <td colspan="3" class="px-6 py-4 text-right">Grand Total</td>
                                <td class="px-6 py-4 text-right">{{ number_format($purchase->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    @if($purchase->status === 'verified')
                        <div class="mt-8 text-center border-t pt-4">
                            <h4 class="font-bold mb-2">Barcode Validation Code</h4>
                            <!-- Placeholder barcode -->
                            <div class="inline-block bg-gray-200 px-4 py-2 font-mono tracking-widest border border-gray-400">
                                ||| || ||| {{ $purchase->grn_number }} ||| || |||
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Scan to verify labeling</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
