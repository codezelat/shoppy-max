<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Receive Courier Payment') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="paymentForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Selection Step -->
                    <form method="GET" action="{{ route('courier-payments.create') }}" class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Select Courier</label>
                        <div class="flex gap-4">
                            <select name="courier_id" class="shadow appearance-none border rounded w-full md:w-1/3 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Choose Courier</option>
                                @foreach($couriers as $courier)
                                    <option value="{{ $courier->id }}" {{ $selectedCourierId == $courier->id ? 'selected' : '' }}>{{ $courier->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-gray-600 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded">Load Orders</button>
                        </div>
                    </form>

                    @if($selectedCourierId && $orders->isNotEmpty())
                    <form action="{{ route('courier-payments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="courier_id" value="{{ $selectedCourierId }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Payment Date</label>
                                <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Total Amount Received</label>
                                <input type="number" name="amount" step="0.01" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Reference / Cheque #</label>
                                <input type="text" name="reference_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>

                        <h3 class="font-bold mb-4">Select Delivered Orders to Cover</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2"><input type="checkbox" @change="toggleAll($event)"></th>
                                        <th class="px-4 py-2 text-left">Order #</th>
                                        <th class="px-4 py-2 text-left">Date</th>
                                        <th class="px-4 py-2 text-left">Customer</th>
                                        <th class="px-4 py-2 text-right">Order Amount</th>
                                        <th class="px-4 py-2 text-right">Courier Cost (Real)</th>
                                        <th class="px-4 py-2 text-right">Delivery Fee (Charged)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($orders as $index => $order)
                                        <tr>
                                            <td class="px-4 py-2">
                                                <input type="checkbox" name="orders[{{ $index }}][id]" value="{{ $order->id }}" class="item-checkbox">
                                            </td>
                                            <td class="px-4 py-2 font-bold">{{ $order->order_number }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $order->created_at->format('Y-m-d') }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $order->customer_name }}</td>
                                            <td class="px-4 py-2 text-right">{{ number_format($order->total_amount, 2) }}</td>
                                            
                                            <!-- Editable Costs -->
                                            <td class="px-4 py-2">
                                                <input type="number" name="orders[{{ $index }}][courier_cost]" step="0.01" value="{{ $order->courier_cost }}" class="w-24 border rounded px-2 py-1 text-right">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" name="orders[{{ $index }}][delivery_fee]" step="0.01" value="{{ $order->delivery_fee }}" class="w-24 border rounded px-2 py-1 text-right">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-green-600 hover:bg-green-800 text-white font-bold py-3 px-6 rounded shadow">
                                Record Payment & Reconcile
                            </button>
                        </div>
                    </form>
                    @elseif($selectedCourierId)
                        <div class="text-center py-8 text-gray-500">
                            No pending 'COD' orders found for this courier (must be Dispatched or Delivered).
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        function paymentForm() {
            return {
                toggleAll(event) {
                    document.querySelectorAll('.item-checkbox').forEach(box => {
                        box.checked = event.target.checked;
                    });
                }
            }
        }
    </script>
</x-app-layout>
