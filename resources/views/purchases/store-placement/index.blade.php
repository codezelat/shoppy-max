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

    @if($racks->isEmpty())
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-900/20 dark:text-amber-200">
            Create at least one rack row before adding stock to {{ strtolower($storeLabel) }}.
            <a href="{{ route('purchases.store-racks.index', $store) }}" class="font-medium underline">Create rack row</a>
        </div>
    @else
        <div
            class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800"
            x-data="storePlacementScanner({
                scanUrl: @js(route('purchases.store-placement.scan', $store)),
                csrfToken: @js(csrf_token()),
                racks: @js($racks->map(fn ($rack) => ['id' => $rack->id, 'label' => $rack->display_label])->values()),
            })"
        >
            <div class="grid gap-4 lg:grid-cols-[minmax(0,360px)_minmax(0,1fr)]">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Rack</label>
                    <div class="relative" @click.outside="closeRackList()">
                        <div class="flex rounded-lg shadow-sm">
                            <div class="relative min-w-0 flex-1">
                                <input
                                    type="search"
                                    x-ref="rackInput"
                                    x-model="rackQuery"
                                    @input="onRackInput()"
                                    @focus="openRackList()"
                                    @keydown.arrow-down.prevent="moveRackHighlight(1)"
                                    @keydown.arrow-up.prevent="moveRackHighlight(-1)"
                                    @keydown.enter.prevent="selectHighlightedRack()"
                                    @keydown.escape="closeRackList()"
                                    role="combobox"
                                    aria-autocomplete="list"
                                    :aria-expanded="rackOpen.toString()"
                                    :aria-activedescendant="highlightedRackId()"
                                    class="block w-full rounded-l-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:z-10 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    placeholder="Search and select rack row"
                                    autocomplete="off"
                                >
                                <button
                                    type="button"
                                    x-show="selectedRackId"
                                    x-cloak
                                    @click="clearRack()"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                                    title="Clear rack"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <button
                                type="button"
                                @click="start()"
                                :disabled="!selectedRackId"
                                class="inline-flex shrink-0 items-center rounded-r-lg border border-l-0 border-blue-700 bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:z-10 focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:border-gray-300 disabled:bg-gray-300 disabled:text-gray-500 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 dark:disabled:border-gray-600 dark:disabled:bg-gray-700 dark:disabled:text-gray-400"
                            >
                                <span x-text="active ? 'Adding' : 'Use Rack'"></span>
                            </button>
                        </div>

                        <div
                            x-show="rackOpen"
                            x-cloak
                            class="absolute z-20 mt-1 max-h-64 w-full overflow-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800"
                            role="listbox"
                        >
                            <template x-for="(rack, index) in filteredRacks()" :key="rack.id">
                                <button
                                    type="button"
                                    :id="`rack-option-${rack.id}`"
                                    @mousedown.prevent="selectRack(rack)"
                                    class="flex w-full items-center justify-between px-3 py-2 text-left text-sm"
                                    :class="index === highlightedRackIndex ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700'"
                                    role="option"
                                    :aria-selected="String(rack.id) === String(selectedRackId)"
                                >
                                    <span class="truncate" x-text="rack.label"></span>
                                    <svg x-show="String(rack.id) === String(selectedRackId)" class="ml-2 h-4 w-4 shrink-0 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"></path>
                                    </svg>
                                </button>
                            </template>
                            <div x-show="filteredRacks().length === 0" class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">No rack found</div>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center justify-between gap-2 text-xs">
                        <span class="truncate text-gray-500 dark:text-gray-400">Selected: <span class="font-medium text-gray-700 dark:text-gray-200" x-text="selectedRackLabel || '-'"></span></span>
                        <span x-show="rackQuery && !selectedRackId" class="shrink-0 text-amber-600 dark:text-amber-300">Pick a rack from the list</span>
                    </div>
                </div>

                <div>
                    <label for="store-placement-scan" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Scan SKU Barcode</label>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <input
                            id="store-placement-scan"
                            x-ref="scanInput"
                            type="text"
                            x-model="barcode"
                            @input="onBarcodeInput($event)"
                            @keydown.enter.prevent="submitScan()"
                            :disabled="!active || submitting"
                            class="block min-w-0 flex-1 rounded-lg border border-gray-300 bg-gray-50 p-2.5 font-mono text-sm text-gray-900 focus:border-emerald-500 focus:ring-emerald-500 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            autocomplete="off"
                            inputmode="text"
                        >
                        <button type="button" @click="submitScan()" :disabled="!active || submitting || barcode.trim() === ''" class="inline-flex items-center justify-center rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-emerald-600 dark:hover:bg-emerald-700">
                            Add
                        </button>
                    </div>
                    <div class="mt-3 rounded-lg border p-3 text-sm" :class="statusOk ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-200' : 'border-gray-200 bg-gray-50 text-gray-600 dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-300'">
                        <span x-text="message"></span>
                    </div>
                    <div class="mt-3 grid gap-2 sm:grid-cols-3">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-900/30">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Active Rack</p>
                            <p class="truncate font-medium text-gray-900 dark:text-white" x-text="selectedRackLabel || '-'"></p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-900/30">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Last SKU</p>
                            <p class="truncate font-medium text-gray-900 dark:text-white" x-text="lastSku || '-'"></p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-900/30">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Scans</p>
                            <p class="font-medium text-gray-900 dark:text-white" x-text="scanCount"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="logs.length > 0" x-cloak class="mt-4 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-left text-xs text-gray-500 dark:text-gray-400">
                    <thead class="bg-gray-50 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-3 py-2">SKU</th>
                            <th class="px-3 py-2">Purchase</th>
                            <th class="px-3 py-2">Product</th>
                            <th class="px-3 py-2">Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="log in logs" :key="log.key">
                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                <td class="px-3 py-2 font-mono" x-text="log.sku"></td>
                                <td class="px-3 py-2" x-text="log.purchase"></td>
                                <td class="px-3 py-2" x-text="log.product"></td>
                                <td class="px-3 py-2" x-text="log.remaining"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
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

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <table class="w-full min-w-[980px] text-left text-sm text-gray-500 dark:text-gray-400">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-6 py-3">Purchase</th>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">SKU / Unit</th>
                        <th class="px-6 py-3">Purchased</th>
                        <th class="px-6 py-3">Placed</th>
                        <th class="px-6 py-3">Remaining</th>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No verified purchase items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-6 py-4">
                {{ $items->links() }}
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            function storePlacementScanner(config) {
                return {
                    racks: config.racks || [],
                    scanUrl: config.scanUrl,
                    csrfToken: config.csrfToken,
                    rackQuery: '',
                    selectedRackId: null,
                    selectedRackLabel: '',
                    rackOpen: false,
                    highlightedRackIndex: 0,
                    active: false,
                    submitting: false,
                    barcode: '',
                    barcodeInputStartedAt: null,
                    barcodeAutoSubmitTimer: null,
                    message: 'Select a rack and start adding.',
                    statusOk: false,
                    lastSku: '',
                    scanCount: 0,
                    logs: [],
                    filteredRacks() {
                        const query = this.rackQuery.trim().toLowerCase();
                        if (!query) {
                            return this.racks;
                        }

                        return this.racks.filter((rack) => rack.label.toLowerCase().includes(query));
                    },
                    highlightedRackId() {
                        const rack = this.filteredRacks()[this.highlightedRackIndex];
                        return rack ? `rack-option-${rack.id}` : null;
                    },
                    openRackList() {
                        this.rackOpen = true;
                        this.highlightedRackIndex = 0;
                    },
                    closeRackList() {
                        this.rackOpen = false;
                        this.highlightedRackIndex = 0;
                    },
                    onRackInput() {
                        this.rackOpen = true;
                        this.highlightedRackIndex = 0;

                        if (this.selectedRackLabel && this.rackQuery !== this.selectedRackLabel) {
                            this.selectedRackId = null;
                            this.selectedRackLabel = '';
                            this.active = false;
                            this.statusOk = false;
                            this.message = 'Pick a rack from the list.';
                        }
                    },
                    moveRackHighlight(direction) {
                        const racks = this.filteredRacks();
                        if (racks.length === 0) {
                            this.highlightedRackIndex = 0;
                            return;
                        }

                        this.rackOpen = true;
                        this.highlightedRackIndex = (this.highlightedRackIndex + direction + racks.length) % racks.length;
                    },
                    selectHighlightedRack() {
                        const rack = this.filteredRacks()[this.highlightedRackIndex];
                        if (rack) {
                            this.selectRack(rack);
                        }
                    },
                    selectRack(rack) {
                        this.selectedRackId = rack.id;
                        this.selectedRackLabel = rack.label;
                        this.rackQuery = rack.label;
                        this.closeRackList();
                        this.statusOk = false;
                        this.message = `Rack selected: ${rack.label}`;
                    },
                    clearRack() {
                        this.selectedRackId = null;
                        this.selectedRackLabel = '';
                        this.rackQuery = '';
                        this.active = false;
                        this.statusOk = false;
                        this.message = 'Select a rack and start adding.';
                        this.$nextTick(() => this.$refs.rackInput?.focus());
                    },
                    start() {
                        if (!this.selectedRackId) {
                            this.statusOk = false;
                            this.message = 'Select a rack first.';
                            return;
                        }

                        this.active = true;
                        this.statusOk = true;
                        this.message = `Adding to ${this.selectedRackLabel}`;
                        this.$nextTick(() => this.$refs.scanInput?.focus());
                    },
                    onBarcodeInput(event) {
                        window.clearTimeout(this.barcodeAutoSubmitTimer);

                        const code = this.barcode.trim();
                        if (!this.active || this.submitting || code === '') {
                            this.barcodeInputStartedAt = null;
                            return;
                        }

                        const now = Date.now();
                        if (!this.barcodeInputStartedAt || code.length <= 1) {
                            this.barcodeInputStartedAt = now;
                        }

                        const elapsed = now - this.barcodeInputStartedAt;
                        const scannerLikeInput = code.length >= 4
                            && (
                                event?.inputType === 'insertFromPaste'
                                || event?.inputType === 'insertReplacementText'
                                || elapsed <= Math.max(700, code.length * 50)
                            );

                        if (!scannerLikeInput) {
                            return;
                        }

                        this.barcodeAutoSubmitTimer = window.setTimeout(() => {
                            if (this.active && !this.submitting && this.barcode.trim() === code) {
                                this.submitScan();
                            }
                        }, 150);
                    },
                    async submitScan() {
                        const code = this.barcode.trim();
                        if (!this.active || this.submitting || code === '') {
                            return;
                        }

                        window.clearTimeout(this.barcodeAutoSubmitTimer);
                        this.barcodeAutoSubmitTimer = null;
                        this.barcodeInputStartedAt = null;
                        this.submitting = true;
                        this.barcode = '';

                        try {
                            const response = await fetch(this.scanUrl, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken,
                                },
                                body: JSON.stringify({
                                    store_rack_id: this.selectedRackId,
                                    barcode: code,
                                }),
                            });
                            const data = await response.json();

                            if (!response.ok || !data.success) {
                                this.statusOk = false;
                                this.message = data.message || 'Unable to add scanned stock.';
                                return;
                            }

                            this.statusOk = true;
                            this.lastSku = data.sku || code;
                            this.scanCount += 1;
                            this.message = data.message || 'Stock added.';
                            this.logs.unshift({
                                key: `${Date.now()}-${this.scanCount}`,
                                sku: data.sku || code,
                                purchase: data.purchase_number || '-',
                                product: data.product_name || '-',
                                remaining: data.remaining_count ?? '-',
                            });
                            this.logs = this.logs.slice(0, 8);
                        } catch (error) {
                            this.statusOk = false;
                            this.message = 'Unable to add scanned stock.';
                        } finally {
                            this.submitting = false;
                            this.$nextTick(() => this.$refs.scanInput?.focus());
                        }
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
