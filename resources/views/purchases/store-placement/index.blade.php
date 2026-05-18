<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Add to {{ $storeLabel }}</h2>
                <nav class="mt-1 flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                        <li><a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">Dashboard</a></li>
                        <li class="text-gray-400">/</li>
                        <li><a href="{{ route('purchases.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">Purchases</a></li>
                        <li class="text-gray-400">/</li>
                        <li><span class="text-sm font-medium text-gray-500 dark:text-gray-400">Add to {{ $storeLabel }}</span></li>
                    </ol>
                </nav>
            </div>
        </div>
    </x-slot>

    @include('purchases.partials.store-tabs', ['store' => $store])

    <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800 dark:border-blue-900 dark:bg-blue-900/20 dark:text-blue-200">
        Stock is updated only when quantity is manually placed into a {{ strtolower($storeLabel) }} rack row.
    </div>

    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form method="GET" action="{{ route('purchases.store-placement.index', $store) }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"></path>
                    </svg>
                </div>
                <input type="search" name="search" value="{{ $search }}" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Search purchase, product, or SKU">
            </div>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700">Search</button>
            <a href="{{ route('purchases.store-placement.index', $store) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Clear</a>
        </form>
    </div>

    @if($racks->isEmpty())
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-900/20 dark:text-amber-200">
            Create at least one rack row before adding stock to {{ strtolower($storeLabel) }}.
            <a href="{{ route('purchases.store-racks.index', $store) }}" class="font-medium underline">Create rack row</a>
        </div>
    @else
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <table class="w-full min-w-[1080px] text-left text-sm text-gray-500 dark:text-gray-400">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-6 py-3">Purchase</th>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">SKU / Unit</th>
                        <th class="px-6 py-3">Purchased</th>
                        <th class="px-6 py-3">Placed</th>
                        <th class="px-6 py-3">Remaining</th>
                        <th class="px-6 py-3">Place Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $placed = $item->placedUnitCount();
                            $remaining = $item->remainingPlacementQuantity();
                            $variantLabel = trim(collect([$item->variant?->unit_value, $item->variant?->unit?->short_name ?: $item->variant?->unit?->name])->filter()->implode(' '));
                        @endphp
                        <tr class="border-b bg-white align-top dark:border-gray-700 dark:bg-gray-800">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $item->purchase?->purchase_number }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->purchase?->supplier?->business_name ?: $item->purchase?->supplier?->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst((string) $item->purchase?->status) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $item->variant?->sku ?: '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $variantLabel !== '' ? $variantLabel : '-' }}</div>
                            </td>
                            <td class="px-6 py-4">{{ number_format((int) $item->quantity) }}</td>
                            <td class="px-6 py-4">{{ number_format($placed) }}</td>
                            <td class="px-6 py-4">
                                <span class="{{ $remaining > 0 ? 'font-semibold text-emerald-700 dark:text-emerald-300' : 'text-gray-500 dark:text-gray-400' }}">{{ number_format($remaining) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($remaining > 0)
                                    <form method="POST" action="{{ route('purchases.store-placement.store', $store) }}" class="flex flex-col gap-2 xl:flex-row xl:items-center">
                                        @csrf
                                        <input type="hidden" name="purchase_item_id" value="{{ $item->id }}">
                                        <select name="store_rack_id" required class="min-w-40 rounded-lg border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                            @foreach($racks as $rack)
                                                <option value="{{ $rack->id }}">{{ $rack->row_name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" name="quantity" min="1" max="{{ $remaining }}" value="{{ $remaining }}" required class="w-24 rounded-lg border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-700 px-3 py-2 text-xs font-medium text-white hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 dark:bg-emerald-600 dark:hover:bg-emerald-700">
                                            Add
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Fully placed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No verified purchase items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-6 py-4">
                {{ $items->links() }}
            </div>
        </div>
    @endif
</x-app-layout>
