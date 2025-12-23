<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Call List (Confirmation Queue)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <p class="mb-4 text-gray-600">Pending or On Hold orders that require phone confirmation.</p>

                    <!-- Order Table -->
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $order->customer_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-blue-600 font-bold">{{ $order->customer_phone }}</td>
                                    <td class="px-6 py-4">
                                        {{ $order->items->count() }} items
                                        <span class="text-xs text-gray-500 block">({{ number_format($order->total_amount, 2) }})</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $order->status === 'on_hold' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2">
                                            <form action="{{ route('orders.status.update', $order->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">Confirm</button>
                                            </form>
                                            <form action="{{ route('orders.status.update', $order->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="on_hold">
                                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">Hold</button>
                                            </form>
                                            <form action="{{ route('orders.status.update', $order->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">Cancel</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No orders needing confirmation.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
