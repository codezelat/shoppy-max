@props([
    'units' => collect(),
    'title' => 'Tracked Labels',
    'emptyLabel' => 'No labels generated',
    'showSource' => false,
    'showStatus' => false,
    'buttonText' => 'View All',
])

@php
    $unitCollection = collect($units)
        ->reject(fn ($unit) => strtolower((string) ($unit->status ?? '')) === \App\Models\InventoryUnit::STATUS_ARCHIVED)
        ->sortBy('id')
        ->values();

    $count = $unitCollection->count();
    $firstCode = $unitCollection->first()?->unit_code;
    $lastCode = $unitCollection->last()?->unit_code;
    $rangeLabel = $count === 0
        ? $emptyLabel
        : ($count === 1 || $firstCode === $lastCode ? $firstCode : $firstCode . ' to ' . $lastCode);
    $modalId = 'inventory-unit-summary-' . uniqid();
@endphp

@if($count > 0)
    <div x-data="{ open: false }" class="space-y-2">
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                {{ number_format($count) }} label{{ $count === 1 ? '' : 's' }}
            </span>
            <span class="font-mono text-[11px] text-gray-600 dark:text-gray-300">{{ $rangeLabel }}</span>
            <button
                type="button"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-2.5 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                @click="open = true"
                aria-controls="{{ $modalId }}"
            >
                {{ $buttonText }}
            </button>
        </div>

        <div
            x-cloak
            x-show="open"
            x-transition.opacity
            id="{{ $modalId }}"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 px-4 py-6"
            @keydown.escape.window="open = false"
            @click.self="open = false"
        >
            <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-800">
                <div class="flex items-start justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ number_format($count) }} label{{ $count === 1 ? '' : 's' }} from {{ $rangeLabel }}
                        </p>
                    </div>
                    <button
                        type="button"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-500 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                        @click="open = false"
                        aria-label="Close label list"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="max-h-[65vh] overflow-y-auto px-5 py-4">
                    <div class="space-y-2">
                        @foreach($unitCollection as $trackedUnit)
                            @php
                                $status = strtolower((string) ($trackedUnit->status ?? ''));
                                $statusLabel = ucfirst(str_replace('_', ' ', $trackedUnit->status ?? ''));
                                $statusClass = match ($status) {
                                    'available' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                    'grn_scanned' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                    'pending_receipt' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                    'allocated' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                    'delivered' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                                    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                };
                            @endphp

                            <div class="flex flex-col gap-2 rounded-xl border border-gray-200 px-4 py-3 dark:border-gray-700">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="font-mono text-sm text-gray-900 dark:text-white">{{ $trackedUnit->unit_code }}</span>
                                    @if($showStatus)
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-medium {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    @endif
                                </div>

                                @if($showSource)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $trackedUnit->purchase?->purchase_number ? 'Source: ' . $trackedUnit->purchase->purchase_number : 'Source: Legacy stock' }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $emptyLabel }}</span>
@endif
