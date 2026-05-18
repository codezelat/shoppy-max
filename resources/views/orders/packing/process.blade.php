<x-app-layout>
    @php
        $summaryItems = collect($packingSummary['items'] ?? []);
        $requiredTotal = $summaryItems->sum('required_count');
        $scannedTotal = $summaryItems->sum('scanned_count');
        $allocatedTotal = $summaryItems->sum(fn ($item) => count($item['units'] ?? []));
        $progressPercent = $requiredTotal > 0 ? min(100, (int) floor(($scannedTotal / $requiredTotal) * 100)) : 0;
        $deliveryStatus = strtolower((string) ($order->delivery_status ?? 'waybill_printed'));
        $backRoute = match ($deliveryStatus) {
            'picked_from_rack' => route('orders.packing.picking'),
            'packed' => route('orders.packing.packed'),
            default => route('orders.packing.ready'),
        };
        $statusLabels = [
            'waybill_printed' => 'Ready To Pick',
            'picked_from_rack' => 'Picking',
            'packed' => 'Packed',
        ];
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Pack Order {{ $order->order_number }}
                </h2>
                <nav class="mt-1 flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li>
                            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">Dashboard</a>
                        </li>
                        <li class="text-gray-400">/</li>
                        <li>
                            <a href="{{ $backRoute }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">Packing</a>
                        </li>
                        <li class="text-gray-400">/</li>
                        <li class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $order->order_number }}</li>
                    </ol>
                </nav>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ $backRoute }}" class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    Back
                </a>
                <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    View Order
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="packer(@js($packingSummary))" x-init="$nextTick(() => $refs.scanInput?.focus())">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Waybill</p>
                <p class="mt-1 font-mono text-lg font-semibold text-gray-900 dark:text-white">{{ $order->waybill_number ?: '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Customer</p>
                <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $order->customer_name ?: ($order->customer->name ?? '-') }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $order->customer_phone ?: ($order->customer->mobile ?? '-') }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white" x-text="statusLabel()">{{ $statusLabels[$deliveryStatus] ?? ucfirst(str_replace('_', ' ', $deliveryStatus)) }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Progress</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white" x-text="`${scannedTotal()} / ${requiredTotal()} scanned`">{{ $scannedTotal }} / {{ $requiredTotal }} scanned</p>
                <div class="mt-2 h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                    <div class="h-2 rounded-full bg-blue-600" :class="allPacked ? 'bg-emerald-500' : 'bg-blue-600'" :style="`width: ${progressPercent()}%`" style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
        </div>

        @if($allocatedTotal < $requiredTotal)
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-200">
                This order has {{ number_format($allocatedTotal) }} allocated labels for {{ number_format($requiredTotal) }} required units. Recheck stock allocation before completing packing.
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/30">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Pick List</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left text-sm text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-100 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">Label</th>
                                <th class="px-4 py-3">Pick Location</th>
                                <th class="px-4 py-3">GRN</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($summaryItems as $item)
                                @forelse(collect($item['units'] ?? []) as $unit)
                                    <tr class="bg-white dark:bg-gray-800" :class="unitScanned(@js($unit['unit_code'])) ? 'bg-emerald-50 dark:bg-emerald-900/10' : ''">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $item['product_name'] ?? '-' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $item['sku'] ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 font-mono text-gray-900 dark:text-white">{{ $unit['unit_code'] ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $unit['store_label'] ?? 'Unassigned Store' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $unit['rack_label'] ?? 'Unassigned Rack' }}</div>
                                        </td>
                                        <td class="px-4 py-3">{{ $unit['purchase_number'] ?? 'Legacy stock' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium" :class="unitScanned(@js($unit['unit_code'])) ? 'border-emerald-300 bg-emerald-100 text-emerald-800 dark:border-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'border-amber-300 bg-amber-100 text-amber-800 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-300'" x-text="unitScanned(@js($unit['unit_code'])) ? 'Scanned' : 'Pending'">
                                                {{ ! empty($unit['scanned']) ? 'Scanned' : 'Pending' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white dark:bg-gray-800">
                                        <td class="px-4 py-4">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $item['product_name'] ?? '-' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $item['sku'] ?? '-' }}</div>
                                        </td>
                                        <td colspan="4" class="px-4 py-4 text-amber-700 dark:text-amber-300">No allocated labels found for this item.</td>
                                    </tr>
                                @endforelse
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No order items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <label for="packing-scan-input" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Scan Label Or SKU</label>
                    <input
                        id="packing-scan-input"
                        x-ref="scanInput"
                        x-model="scanInput"
                        @input="onScanInput($event)"
                        @keydown.enter.prevent="scanItem()"
                        :disabled="scannerDisabled"
                        type="text"
                        autofocus
                        autocomplete="off"
                        class="block w-full rounded-lg border border-indigo-500 bg-gray-50 p-3 font-mono text-lg text-gray-900 focus:border-indigo-600 focus:ring-indigo-600 disabled:cursor-not-allowed disabled:opacity-60 dark:border-indigo-500 dark:bg-gray-700 dark:text-white"
                    >
                    <button
                        type="button"
                        @click="scanItem()"
                        :disabled="scannerDisabled || !scanInput.trim()"
                        class="mt-3 inline-flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-3 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-blue-600 dark:hover:bg-blue-700"
                    >
                        Scan Now
                    </button>
                    <div class="mt-3 rounded-lg border px-3 py-2 text-sm" :class="statusOk ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-200' : 'border-gray-200 bg-gray-50 text-gray-600 dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-300'">
                        <span x-text="message"></span>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Items</h3>
                    <div class="mt-3 space-y-3">
                        <template x-for="item in items" :key="item.id">
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="item.product_name || '-'"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="item.sku || '-'"></p>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="`${item.scanned_count || 0} / ${item.qty || 0}`"></span>
                                </div>
                                <div class="mt-2 h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div class="h-2 rounded-full bg-blue-600" :class="Number(item.scanned_count || 0) >= Number(item.qty || 0) ? 'bg-emerald-500' : 'bg-blue-600'" :style="`width: ${item.qty ? Math.min(100, Math.floor((Number(item.scanned_count || 0) / Number(item.qty || 0)) * 100)) : 0}%`"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Packing Result</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400" x-text="resultText()">
                        {{ $deliveryStatus === 'packed' ? 'This order is packed.' : 'Scan every allocated label. The order will move to Packed automatically on the final scan.' }}
                    </p>
                    <a
                        href="{{ route('orders.packing.packed') }}"
                        x-show="currentStatus === 'packed'"
                        x-cloak
                        class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-3 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700"
                    >
                        View Packed Queue
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function packer(initialSummary) {
            return {
                scanInput: '',
                currentStatus: @json((string) ($order->delivery_status ?? 'waybill_printed')),
                scanning: false,
                statusOk: false,
                message: @json($deliveryStatus === 'packed' ? 'This order is already packed.' : 'Ready to scan.'),
                scanStartedAt: null,
                scanAutoSubmitTimer: null,
                packedQueueUrl: @json(route('orders.packing.packed')),
                items: (initialSummary.items || []).map((item) => ({
                    id: item.order_item_id,
                    sku: item.sku,
                    product_name: item.product_name,
                    qty: Number(item.required_count || 0),
                    scanned_count: Number(item.scanned_count || 0),
                    scanned_codes: Array.isArray(item.scanned_codes) ? item.scanned_codes : [],
                    units: Array.isArray(item.units) ? item.units : [],
                })),
                statusLabel() {
                    const labels = {
                        waybill_printed: 'Ready To Pick',
                        picked_from_rack: 'Picking',
                        packed: 'Packed',
                    };

                    return labels[this.currentStatus] || 'Processing';
                },
                requiredTotal() {
                    return this.items.reduce((total, item) => total + Number(item.qty || 0), 0);
                },
                scannedTotal() {
                    return this.items.reduce((total, item) => total + Number(item.scanned_count || 0), 0);
                },
                progressPercent() {
                    const required = this.requiredTotal();
                    return required > 0 ? Math.min(100, Math.floor((this.scannedTotal() / required) * 100)) : 0;
                },
                get allPacked() {
                    return this.items.length > 0 && this.items.every((item) => Number(item.scanned_count || 0) >= Number(item.qty || 0));
                },
                get scannerDisabled() {
                    return this.scanning || this.currentStatus === 'packed' || this.allPacked;
                },
                resultText() {
                    if (this.currentStatus === 'packed') {
                        return 'All labels are scanned. This order is packed and ready for dispatch.';
                    }

                    if (this.allPacked) {
                        return 'All labels are scanned. Packing is being finalized.';
                    }

                    return 'Scan every allocated label. The order will move to Packed automatically on the final scan.';
                },
                unitScanned(unitCode) {
                    return this.items.some((item) => (item.units || []).some((unit) => unit.unit_code === unitCode && unit.scanned));
                },
                notify(type, message) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: type,
                            text: message,
                            timer: 2200,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end',
                        });
                    }
                },
                onScanInput(event) {
                    window.clearTimeout(this.scanAutoSubmitTimer);

                    const code = this.scanInput.trim();
                    if (!code || this.scannerDisabled) {
                        this.scanStartedAt = null;
                        return;
                    }

                    const now = Date.now();
                    if (!this.scanStartedAt || code.length <= 1) {
                        this.scanStartedAt = now;
                    }

                    const elapsed = now - this.scanStartedAt;
                    const scannerLikeInput = code.length >= 4
                        && (
                            event?.inputType === 'insertFromPaste'
                            || event?.inputType === 'insertReplacementText'
                            || elapsed <= Math.max(700, code.length * 50)
                        );

                    if (!scannerLikeInput) {
                        return;
                    }

                    this.scanAutoSubmitTimer = window.setTimeout(() => {
                        if (!this.scanning && this.scanInput.trim() === code) {
                            this.scanItem();
                        }
                    }, 150);
                },
                async scanItem() {
                    const code = this.scanInput.trim();
                    if (!code || this.scannerDisabled) {
                        return;
                    }

                    window.clearTimeout(this.scanAutoSubmitTimer);
                    this.scanAutoSubmitTimer = null;
                    this.scanStartedAt = null;
                    this.scanning = true;

                    try {
                        const response = await fetch(@json(route('orders.packing.scan', $order->id)), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token()),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ unit_code: code }),
                        });
                        const data = await response.json().catch(() => ({}));

                        if (!response.ok || !data.success) {
                            this.statusOk = false;
                            this.message = data.message || 'Failed to scan item.';
                            this.notify('error', this.message);
                            return;
                        }

                        this.currentStatus = data.delivery_status || this.currentStatus;
                        if (data.summary && Array.isArray(data.summary.items)) {
                            this.items = data.summary.items.map((item) => ({
                                id: item.order_item_id,
                                sku: item.sku,
                                product_name: item.product_name,
                                qty: Number(item.required_count || 0),
                                scanned_count: Number(item.scanned_count || 0),
                                scanned_codes: Array.isArray(item.scanned_codes) ? item.scanned_codes : [],
                                units: Array.isArray(item.units) ? item.units : [],
                            }));
                        }

                        this.statusOk = true;
                        this.message = data.message || `${data.unit_code || code} matched.`;
                        this.notify('success', this.message);

                        if (data.auto_packed || this.currentStatus === 'packed') {
                            window.setTimeout(() => {
                                window.location.href = this.packedQueueUrl;
                            }, 700);
                        }
                    } catch (error) {
                        this.statusOk = false;
                        this.message = 'Failed to scan item.';
                        this.notify('error', this.message);
                    } finally {
                        this.scanning = false;
                        this.scanInput = '';
                        this.$nextTick(() => this.$refs.scanInput?.focus());
                    }
                },
            };
        }
    </script>
</x-app-layout>
