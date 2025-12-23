<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Print Waybills') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('orders.waybill.print') }}" method="POST" target="_blank">
                        @csrf
                        
                        <div class="mb-4 flex justify-between items-center">
                            <h3 class="text-lg font-medium">Select Orders to Print</h3>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded">
                                Print Selected (4-up A4)
                            </button>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">City</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        </td>
                                        <td class="px-6 py-4 font-bold">{{ $order->order_number }}</td>
                                        <td class="px-6 py-4">{{ $order->customer_name }}</td>
                                        <td class="px-6 py-4">{{ $order->city->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No confirmed orders ready for printing.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        <div class="mt-4">
                            {{ $orders->links() }}
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.order-checkbox');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });
    </script>
</x-app-layout>
