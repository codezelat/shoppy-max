@php
    $storeTabs = [
        ['label' => 'Add to Retail Store', 'route' => route('purchases.store-placement.index', 'retail'), 'active' => request()->routeIs('purchases.store-placement.*') && ($store ?? null) === 'retail'],
        ['label' => 'Retail Racks', 'route' => route('purchases.store-racks.index', 'retail'), 'active' => request()->routeIs('purchases.store-racks.*') && ($store ?? null) === 'retail'],
        ['label' => 'Add to Warehouse Store', 'route' => route('purchases.store-placement.index', 'warehouse'), 'active' => request()->routeIs('purchases.store-placement.*') && ($store ?? null) === 'warehouse'],
        ['label' => 'Warehouse Racks', 'route' => route('purchases.store-racks.index', 'warehouse'), 'active' => request()->routeIs('purchases.store-racks.*') && ($store ?? null) === 'warehouse'],
    ];
@endphp

<div class="mb-4 border-b border-gray-200 dark:border-gray-700">
    <nav class="-mb-px flex flex-wrap gap-2 text-sm font-medium">
        @foreach($storeTabs as $tab)
            <a href="{{ $tab['route'] }}"
               class="inline-flex items-center border-b-2 px-3 py-2 {{ $tab['active'] ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>
</div>
