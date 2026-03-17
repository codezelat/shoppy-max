<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">GRN Checking: {{ $purchase->purchase_number }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Scan each received barcode. The purchase completes automatically when every label in this GRN is confirmed.</p>
            </div>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            <svg class="me-2.5 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="mx-1 h-3 w-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                            <a href="{{ route('purchases.moderation.grn') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">GRN Checking</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="mx-1 h-3 w-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                            <span class="ms-1 text-sm font-medium text-gray-500 dark:text-gray-400">{{ $purchase->purchase_number }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    @php
        $totalUnits = $purchase->totalTrackedUnitsCount();
        $receivedUnits = $purchase->grnProgressUnitsCount();
        $pendingUnits = $purchase->pendingReceiptUnitsCount();
        $completionPercent = $totalUnits > 0 ? (int) round(($receivedUnits / $totalUnits) * 100) : 0;
        $isComplete = ($purchase->status ?? 'pending') === 'complete';
        $itemsPayload = $purchase->items->map(function ($item) {
            return [
                'id' => (int) $item->id,
                'scanned_count' => $item->scannedUnitCount(),
                'remaining_count' => $item->pendingUnitCount(),
            ];
        })->keyBy('id');
    @endphp

    <div
        class="space-y-6 p-6"
        x-data="grnScanner({
            scanUrl: '{{ route('purchases.grn.scan', $purchase) }}',
            csrfToken: '{{ csrf_token() }}',
            completed: @js($isComplete),
            purchaseStatus: @js((string) ($purchase->status ?? 'pending')),
            totals: {
                tracked: @js($totalUnits),
                received: @js($receivedUnits),
                pending: @js($pendingUnits),
            },
            items: @js($itemsPayload),
        })"
        x-init="focusScanner()"
    >
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-900/40 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-end gap-2">
            <a href="{{ route('purchases.moderation.grn') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                Back to GRN Checking
            </a>
            <a href="{{ route('purchases.show', $purchase) }}" class="inline-flex items-center rounded-lg border border-blue-300 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300 dark:hover:bg-blue-900/30">
                Open Purchase Details
            </a>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Supplier</p>
                <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $purchase->supplier->business_name ?? $purchase->supplier->name ?? '-' }}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ optional($purchase->purchase_date)->format('d M Y') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Purchasing ID</p>
                <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $purchase->purchase_number }}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $purchase->items->count() }} item{{ $purchase->items->count() === 1 ? '' : 's' }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Scanned Progress</p>
                <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                    <span x-text="totals.received"></span> / <span x-text="totals.tracked"></span>
                </p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    <span x-text="totals.pending"></span> remaining
                </p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Current Status</p>
                <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white" x-text="formatStatus(purchaseStatus)"></p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="completed ? 'All labels scanned and stock received.' : 'Waiting for GRN scan completion.'"></p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Scanner</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Scan each GRN barcode once. Each scan updates GRN progress only. Stock stays unavailable until the final label is scanned and the purchase completes automatically.
                    </p>
                </div>
                <div class="w-full max-w-md">
                    <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                        <div class="h-full rounded-full bg-blue-600 transition-all duration-300" :style="`width: ${progressPercent()}%`"></div>
                    </div>
                    <p class="mt-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                        <span x-text="progressPercent()"></span>% scanned
                    </p>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1fr)_20rem]">
                <div>
                    <form @submit.prevent="submitScan" class="space-y-3">
                        <label for="grn-unit-scan" class="text-sm font-medium text-gray-700 dark:text-gray-200">Scan Barcode</label>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input
                                id="grn-unit-scan"
                                x-ref="scanner"
                                x-model.trim="scanValue"
                                :disabled="completed || submitting"
                                @keydown="handleScanKeydown($event)"
                                @input="handleScanInput($event)"
                                type="text"
                                inputmode="text"
                                autocomplete="off"
                                autocapitalize="characters"
                                spellcheck="false"
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-base text-gray-900 focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:disabled:bg-gray-800"
                                placeholder="Ready for barcode scanner"
                            >
                            <button
                                type="submit"
                                :disabled="completed || submitting || scanValue.length < 3"
                                class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-5 py-3 text-sm font-medium text-white hover:bg-blue-800 disabled:cursor-not-allowed disabled:bg-blue-400 dark:bg-blue-600 dark:hover:bg-blue-700 dark:disabled:bg-blue-800/50"
                            >
                                <span x-show="!submitting">Scan</span>
                                <span x-show="submitting">Scanning...</span>
                            </button>
                        </div>
                    </form>

                    <template x-if="notice.message">
                        <div class="mt-4 rounded-xl border p-4 text-sm"
                             :class="notice.type === 'error'
                                ? 'border-red-200 bg-red-50 text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300'
                                : 'border-green-200 bg-green-50 text-green-700 dark:border-green-900/40 dark:bg-green-900/20 dark:text-green-300'">
                            <div class="font-medium" x-text="notice.message"></div>
                            <template x-if="notice.code">
                                <div class="mt-1 font-mono text-xs opacity-80" x-text="notice.code"></div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Latest Scans</h4>
                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="`${recentScans.length} shown`"></span>
                    </div>
                    <div class="mt-3 space-y-2">
                        <template x-if="recentScans.length === 0">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No labels scanned yet.</p>
                        </template>
                        <template x-for="entry in recentScans" :key="entry.code">
                            <div class="rounded-xl border border-gray-200 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-800">
                                <div class="font-mono text-xs text-gray-900 dark:text-white" x-text="entry.code"></div>
                                <div class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200" x-text="entry.product_name"></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="entry.variant_label || 'Variant not linked'"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            @if($isComplete)
                <div class="mt-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-900/40 dark:bg-green-900/20 dark:text-green-300">
                    This GRN is already complete. All labels were scanned on {{ optional($purchase->completed_at)->format('d M Y h:i A') ?: '-' }}.
                </div>
            @endif
        </div>

        <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Product Name & Variant</th>
                        <th class="px-6 py-3">SKU</th>
                        <th class="px-6 py-3">Labels</th>
                        <th class="px-6 py-3 text-right">PCS Quantity</th>
                        <th class="px-6 py-3 text-right">Scanned</th>
                        <th class="px-6 py-3 text-right">Remaining</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $index => $item)
                        <tr class="border-b border-gray-200 bg-white transition-colors dark:border-gray-700 dark:bg-gray-800" :class="lastScannedItemId === {{ $item->id }} ? 'bg-blue-50 dark:bg-blue-900/10' : ''">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @if($item->variant)
                                        {{ $item->variant->unit_value ? $item->variant->unit_value . ' ' : '' }}{{ $item->variant->unit->name ?? ($item->variant->unit->short_name ?? '-') }}
                                    @else
                                        Variant not linked
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-600 dark:text-gray-300">{{ $item->variant?->sku ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <x-inventory-unit-summary
                                    :units="$item->trackedUnits()"
                                    :title="'Tracked Labels: ' . $item->product_name"
                                />
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">{{ number_format((float) $item->quantity, 0) }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-semibold text-green-700 dark:text-green-300" x-text="itemState({{ $item->id }}, 'scanned_count')"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-semibold" :class="itemState({{ $item->id }}, 'remaining_count') > 0 ? 'text-amber-600 dark:text-amber-300' : 'text-green-600 dark:text-green-300'" x-text="itemState({{ $item->id }}, 'remaining_count')"></span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function grnScanner(config) {
            return {
                scanUrl: config.scanUrl,
                csrfToken: config.csrfToken,
                scanValue: '',
                submitting: false,
                completed: Boolean(config.completed),
                purchaseStatus: config.purchaseStatus,
                totals: config.totals,
                items: config.items,
                recentScans: [],
                notice: {
                    type: 'success',
                    message: '',
                    code: '',
                },
                lastScannedItemId: null,
                autoSubmitTimer: null,
                lastKeyAt: 0,
                rapidKeyCount: 0,

                focusScanner() {
                    if (this.completed) {
                        return;
                    }

                    this.$nextTick(() => this.$refs.scanner?.focus());
                },

                resetAutoSubmitState() {
                    this.lastKeyAt = 0;
                    this.rapidKeyCount = 0;

                    if (this.autoSubmitTimer) {
                        clearTimeout(this.autoSubmitTimer);
                        this.autoSubmitTimer = null;
                    }
                },

                formatStatus(status) {
                    return String(status || 'pending')
                        .replace(/_/g, ' ')
                        .replace(/\b\w/g, (char) => char.toUpperCase());
                },

                progressPercent() {
                    if (!this.totals.tracked) {
                        return 0;
                    }

                    return Math.min(100, Math.round((Number(this.totals.received) / Number(this.totals.tracked)) * 100));
                },

                itemState(itemId, key) {
                    return this.items[itemId]?.[key] ?? 0;
                },

                setNotice(type, message, code = '') {
                    this.notice = { type, message, code };
                },

                handleScanKeydown(event) {
                    if (this.completed || this.submitting) {
                        return;
                    }

                    if (event.key === 'Enter') {
                        event.preventDefault();
                        this.submitScan();
                        return;
                    }

                    if (event.key.length !== 1) {
                        return;
                    }

                    const now = Date.now();
                    this.rapidKeyCount = now - this.lastKeyAt <= 45 ? this.rapidKeyCount + 1 : 1;
                    this.lastKeyAt = now;
                },

                handleScanInput() {
                    if (this.completed || this.submitting) {
                        return;
                    }

                    const code = this.scanValue.trim().toUpperCase();
                    if (code.length < 3) {
                        this.resetAutoSubmitState();
                        return;
                    }

                    if (this.autoSubmitTimer) {
                        clearTimeout(this.autoSubmitTimer);
                    }

                    if (this.rapidKeyCount < 3) {
                        return;
                    }

                    this.autoSubmitTimer = setTimeout(() => {
                        this.autoSubmitTimer = null;
                        this.submitScan();
                    }, 90);
                },

                async submitScan() {
                    if (this.completed || this.submitting) {
                        return;
                    }

                    const code = this.scanValue.trim().toUpperCase();
                    if (code.length < 3) {
                        this.setNotice('error', 'Scan a valid barcode.');
                        this.resetAutoSubmitState();
                        this.focusScanner();
                        return;
                    }

                    this.resetAutoSubmitState();
                    this.submitting = true;

                    try {
                        const response = await fetch(this.scanUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({ unit_code: code }),
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok || !data.success) {
                            this.setNotice('error', data?.message || 'Unable to scan this barcode.', code);
                            return;
                        }

                        this.scanValue = '';
                        this.purchaseStatus = data.purchase.status;
                        this.completed = Boolean(data.completed);
                        this.totals.received = Number(data.purchase.grn_progress_units_count || 0);
                        this.totals.pending = Number(data.purchase.pending_units_count || 0);
                        this.totals.tracked = Number(data.purchase.total_units_count || this.totals.tracked || 0);
                        this.lastScannedItemId = Number(data.item.id);

                        if (this.items[data.item.id]) {
                            this.items[data.item.id].scanned_count = Number(data.item.scanned_count || 0);
                            this.items[data.item.id].remaining_count = Number(data.item.remaining_count || 0);
                        }

                        this.recentScans.unshift({
                            code: data.unit.code,
                            product_name: data.unit.product_name,
                            variant_label: data.unit.variant_label,
                        });
                        this.recentScans = this.recentScans.slice(0, 6);

                        this.setNotice('success', data.message, data.unit.code);
                    } catch (error) {
                        this.setNotice('error', 'Unexpected error while scanning this barcode.', code);
                    } finally {
                        this.submitting = false;
                        this.resetAutoSubmitState();
                        this.focusScanner();
                    }
                },
            };
        }
    </script>
</x-app-layout>
