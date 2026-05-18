<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Packing & Dispatch') }}
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
                            <svg class="mx-1 h-3 w-3 text-gray-400 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Packing</span>
                        </div>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li>
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stageConfig['title'] }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="rounded-md bg-white p-6 shadow-md dark:bg-gray-800">
        <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $stageConfig['title'] }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $stageConfig['description'] }}</p>
            </div>
            <a href="{{ route('orders.index', ['view' => 'active']) }}" class="inline-flex w-full items-center justify-center rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 sm:w-auto">
                Orders
            </a>
        </div>

        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <a href="{{ $stageRoutes['ready'] }}" class="rounded-lg border p-4 transition {{ $stage === 'ready' ? 'border-indigo-300 bg-indigo-50 dark:border-indigo-700 dark:bg-indigo-900/20' : 'border-gray-200 bg-gray-50 hover:border-indigo-300 hover:bg-indigo-50 dark:border-gray-700 dark:bg-gray-900/20 dark:hover:border-indigo-700 dark:hover:bg-indigo-900/20' }}">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Ready To Pick</p>
                <p class="mt-1 text-2xl font-bold text-indigo-700 dark:text-indigo-300">{{ number_format($stats['waybill_printed'] ?? 0) }}</p>
            </a>
            <a href="{{ $stageRoutes['picking'] }}" class="rounded-lg border p-4 transition {{ $stage === 'picking' ? 'border-purple-300 bg-purple-50 dark:border-purple-700 dark:bg-purple-900/20' : 'border-gray-200 bg-gray-50 hover:border-purple-300 hover:bg-purple-50 dark:border-gray-700 dark:bg-gray-900/20 dark:hover:border-purple-700 dark:hover:bg-purple-900/20' }}">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Picking / Scanning</p>
                <p class="mt-1 text-2xl font-bold text-purple-700 dark:text-purple-300">{{ number_format($stats['picked_from_rack'] ?? 0) }}</p>
            </a>
            <a href="{{ $stageRoutes['packed'] }}" class="rounded-lg border p-4 transition {{ $stage === 'packed' ? 'border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/20' : 'border-gray-200 bg-gray-50 hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:bg-gray-900/20 dark:hover:border-blue-700 dark:hover:bg-blue-900/20' }}">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Packed</p>
                <p class="mt-1 text-2xl font-bold text-blue-700 dark:text-blue-300">{{ number_format($stats['packed'] ?? 0) }}</p>
            </a>
        </div>

        <div class="mb-6 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
            <form method="GET" action="{{ match ($stage) { 'picking' => route('orders.packing.picking'), 'packed' => route('orders.packing.packed'), default => route('orders.packing.ready') } }}" class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                <div class="lg:col-span-8">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                    <input id="search" name="search" value="{{ $filters['search'] ?? '' }}" type="text" placeholder="Order, waybill, customer, SKU, label, rack, or GRN" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                </div>
                <div class="lg:col-span-2">
                    <label for="per_page" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Rows</label>
                    <select id="per_page" name="per_page" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach([15, 25, 50, 100] as $size)
                            <option value="{{ $size }}" {{ (int) ($filters['per_page'] ?? 25) === $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2 lg:col-span-2">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700">
                        Filter
                    </button>
                    <a href="{{ match ($stage) { 'picking' => route('orders.packing.picking'), 'packed' => route('orders.packing.packed'), default => route('orders.packing.ready') } }}" class="inline-flex flex-1 items-center justify-center rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col gap-2 border-b border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/30 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $orders->count() }} of {{ $orders->total() }} matching orders
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px] text-left text-sm text-gray-500 dark:text-gray-400">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Waybill / Courier</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Scan Progress</th>
                            <th class="px-4 py-3">Pick Locations</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($orders as $order)
                            @php
                                $deliveryStatus = strtolower((string) ($order->delivery_status ?? 'pending'));
                                $deliveryLabels = [
                                    'waybill_printed' => 'Ready To Pick',
                                    'picked_from_rack' => 'Picking',
                                    'packed' => 'Packed',
                                ];
                                $deliveryClasses = [
                                    'waybill_printed' => 'border-indigo-300 bg-indigo-100 text-indigo-800 dark:border-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
                                    'picked_from_rack' => 'border-purple-300 bg-purple-100 text-purple-800 dark:border-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                                    'packed' => 'border-blue-300 bg-blue-100 text-blue-800 dark:border-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                ];
                                $summary = $order->packing_summary ?? ['items' => [], 'all_scanned' => false];
                                $requiredCount = collect($summary['items'])->sum('required_count');
                                $scannedCount = collect($summary['items'])->sum('scanned_count');
                                $allocatedCount = collect($summary['items'])->sum(fn ($item) => count($item['units'] ?? []));
                                $progressPercent = $requiredCount > 0 ? min(100, (int) floor(($scannedCount / $requiredCount) * 100)) : 0;
                                $pickLocations = collect($summary['items'])
                                    ->flatMap(fn ($item) => collect($item['units'] ?? [])->map(fn ($unit) => [
                                        'location' => ($unit['store_label'] ?? 'Unassigned Store') . ' · ' . ($unit['rack_label'] ?? 'Unassigned Rack'),
                                        'source' => $unit['purchase_number'] ?? 'Legacy stock',
                                    ]))
                                    ->unique(fn ($entry) => $entry['location'] . '|' . $entry['source'])
                                    ->values();
                            @endphp
                            <tr class="bg-white transition-colors hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ optional($order->order_date)->format('d M Y') ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-mono text-gray-900 dark:text-white">{{ $order->waybill_number ?: '-' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->courier?->name ?? 'Courier not assigned' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $order->customer_name ?: ($order->customer->name ?? '-') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->customer_phone ?: ($order->customer->mobile ?? '-') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-between gap-3 text-xs">
                                        <span class="font-medium text-gray-700 dark:text-gray-200">{{ number_format($scannedCount) }} / {{ number_format($requiredCount) }} scanned</span>
                                        <span class="{{ $allocatedCount < $requiredCount ? 'text-amber-700 dark:text-amber-300' : 'text-gray-500 dark:text-gray-400' }}">{{ number_format($allocatedCount) }} allocated</span>
                                    </div>
                                    <div class="mt-2 h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                                        <div class="h-2 rounded-full {{ $progressPercent >= 100 ? 'bg-emerald-500' : 'bg-blue-600' }}" style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($pickLocations->isNotEmpty())
                                        <div class="space-y-1">
                                            @foreach($pickLocations->take(3) as $location)
                                                <div class="text-xs">
                                                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $location['location'] }}</span>
                                                    <span class="text-gray-500 dark:text-gray-400">· GRN: {{ $location['source'] }}</span>
                                                </div>
                                            @endforeach
                                            @if($pickLocations->count() > 3)
                                                <div class="text-xs text-gray-400">+{{ $pickLocations->count() - 3 }} more</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-amber-600 dark:text-amber-300">No allocated rack labels</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium {{ $deliveryClasses[$deliveryStatus] ?? 'border-gray-300 bg-gray-100 text-gray-800' }}">
                                        {{ $deliveryLabels[$deliveryStatus] ?? ucfirst(str_replace('_', ' ', $deliveryStatus)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                            View
                                        </a>
                                        @if($stage === 'packed')
                                            <form action="{{ route('orders.packing.mark-dispatched', $order->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center rounded-lg bg-cyan-600 px-3 py-2 text-xs font-medium text-white hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-300">
                                                    Dispatch
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('orders.packing.process', $order->id) }}" class="inline-flex items-center rounded-lg bg-blue-700 px-3 py-2 text-xs font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700">
                                                {{ $stage === 'ready' ? 'Start Picking' : 'Scan' }}
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                    {{ $stageConfig['empty'] }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
