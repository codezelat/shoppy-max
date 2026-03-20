<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Waybill Print') }} - {{ $courier->name }}
            </h2>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <a href="{{ route('orders.waybill.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Waybill Print</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">{{ $courier->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-gray-800">
        <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Courier Waybill Queue</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Only call-confirmed orders with pending delivery and no waybill appear here. Printing allocates the next available waybill IDs from this courier pool and removes those orders from the queue.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('orders.waybill.index') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-800 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    Back
                </a>
                <button
                    type="submit"
                    form="waybillForm"
                    id="printSelectedBtn"
                    class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled
                >
                    Print Selected
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3 mb-6">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/20">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Eligible To Print</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['eligible'] ?? 0) }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/20">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Call Confirmed Pending Delivery</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['confirm_total'] ?? 0) }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/20">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Has Waybill No.</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['with_waybill'] ?? 0) }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/20">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Available Waybill IDs</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['available_waybills'] ?? 0) }}</p>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Next: {{ $stats['next_available_waybill'] ?? 'None' }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/20">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Shortfall</p>
                <p class="mt-1 text-2xl font-bold {{ ($stats['waybill_shortfall'] ?? 0) > 0 ? 'text-amber-700 dark:text-amber-300' : 'text-emerald-700 dark:text-emerald-300' }}">{{ number_format($stats['waybill_shortfall'] ?? 0) }}</p>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Extra waybill IDs needed to print every eligible order.</p>
            </div>
        </div>

        @if(($stats['waybill_shortfall'] ?? 0) > 0)
            <div class="mb-6 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-700 dark:bg-amber-900/20 dark:text-amber-200">
                This courier has fewer available waybill IDs than eligible orders. You can still print selected orders, but the selected count must not exceed the available waybill quota.
            </div>
        @endif

        <div class="mb-6 rounded-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-5">
            <form method="GET" action="{{ route('orders.waybill.show', $courier) }}">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-10">
                    <div class="md:col-span-2 xl:col-span-4">
                        <label for="search" class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Search Orders</label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Order ID, customer name, or mobile"
                            class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                    </div>
                    <div class="xl:col-span-2">
                        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Call Status</label>
                        <div class="block w-full p-2.5 text-sm text-green-700 border border-green-300 rounded-lg bg-green-50 dark:bg-green-900/20 dark:border-green-700 dark:text-green-300">
                            Call Confirm (fixed)
                        </div>
                    </div>
                    <div class="xl:col-span-1">
                        <label for="per_page" class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Rows</label>
                        <select id="per_page" name="per_page" class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @foreach([25, 50, 100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 25) === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="xl:col-span-1">
                        <label for="date_from" class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Date From</label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="xl:col-span-2">
                        <label for="date_to" class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Date To</label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-3 border-t border-gray-200 pt-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Search supports order ID, customer name, and mobile number. Selection applies only to the currently visible page.</p>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700">
                            Apply Filters
                        </button>
                        <a href="{{ route('orders.waybill.show', $courier) }}" class="inline-flex items-center justify-center rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <form action="{{ route('orders.waybill.print') }}" method="POST" target="_blank" id="waybillForm">
            @csrf
            <input type="hidden" name="courier_id" value="{{ $courier->id }}">

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/30 border-b border-gray-200 dark:border-gray-700 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Showing {{ $orders->count() }} of {{ $orders->total() }} matching orders
                    </p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200" id="selectedCountLabel">0 selected</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3 w-4">
                                    <input type="checkbox" id="selectAll" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                </th>
                                <th scope="col" class="px-4 py-3">Order ID</th>
                                <th scope="col" class="px-4 py-3">Customer</th>
                                <th scope="col" class="px-4 py-3">Mobile</th>
                                <th scope="col" class="px-4 py-3">Address</th>
                                <th scope="col" class="px-4 py-3 text-right">Net Total</th>
                                <th scope="col" class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($orders as $order)
                                @php
                                    $callStatus = strtolower((string) ($order->call_status ?? 'pending'));
                                    $callStatusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                        'hold' => 'bg-orange-100 text-orange-800 border-orange-300',
                                        'confirm' => 'bg-green-100 text-green-800 border-green-300',
                                        'cancel' => 'bg-red-100 text-red-800 border-red-300',
                                    ];
                                @endphp
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->order_date ? \Illuminate\Support\Carbon::parse($order->order_date)->format('d M Y') : '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">
                                        {{ $order->customer_name ?? $order->customer->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                        {{ $order->customer_phone ?? $order->customer->mobile ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 max-w-sm text-gray-700 dark:text-gray-300 break-words">
                                        {{ $order->customer_address ?? $order->customer->address ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                        {{ number_format((float) $order->total_amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="{{ $callStatusColors[$callStatus] ?? 'bg-gray-100 text-gray-800 border-gray-300' }} text-xs font-medium px-2.5 py-0.5 rounded border capitalize">
                                            {{ $order->call_status ?? 'pending' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        No orders found for this courier with current filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        <div class="mt-4">
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('waybillForm');
            const selectAll = document.getElementById('selectAll');
            const selectedCountLabel = document.getElementById('selectedCountLabel');
            const printBtn = document.getElementById('printSelectedBtn');
            const availableCount = {{ (int) ($stats['available_waybills'] ?? 0) }};

            if (!form) {
                return;
            }

            const checkboxes = Array.from(form.querySelectorAll('.order-checkbox'));

            const syncSelection = () => {
                const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;

                if (selectedCountLabel) {
                    selectedCountLabel.textContent = `${checkedCount} selected`;
                }

                if (printBtn) {
                    printBtn.disabled = checkedCount === 0;
                }

                if (selectAll) {
                    selectAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
                }
            };

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach((checkbox) => {
                        checkbox.checked = this.checked;
                    });
                    syncSelection();
                });
            }

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', syncSelection);
            });

            form.addEventListener('submit', (event) => {
                const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;
                if (checkedCount === 0) {
                    event.preventDefault();

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            text: 'Select at least one order to print waybills.',
                        });
                    } else {
                        alert('Select at least one order to print waybills.');
                    }
                    return;
                }

                if (checkedCount > availableCount) {
                    event.preventDefault();

                    const message = `Only ${availableCount} waybill ID${availableCount === 1 ? '' : 's'} are available for this courier. Add more waybill IDs or reduce the selected orders.`;

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            text: message,
                        });
                    } else {
                        alert(message);
                    }
                }
            });

            syncSelection();
        })();
    </script>
</x-app-layout>
