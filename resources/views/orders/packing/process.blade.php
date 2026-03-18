<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pack Order') }} #{{ $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="packer()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex gap-6">
                <!-- Scanner Section -->
                <div class="w-full md:w-2/3 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="mb-6">
                        <label class="block text-gray-700 text-lg font-bold mb-2">Scan Unit Barcode</label>
                        <input type="text" x-ref="scanInput" x-model="scanInput" @keydown.enter.prevent="scanItem()" autofocus class="shadow border-2 border-indigo-500 rounded w-full py-4 px-4 text-gray-700 text-xl leading-tight focus:outline-none focus:shadow-outline" placeholder="Scan item barcode here...">
                        <p class="text-sm text-gray-500 mt-2">Scan the allocated unit code for each piece. First valid scan moves the order to Picked From Rack automatically.</p>
                    </div>

                    <h3 class="text-lg font-bold mb-4">Items to Pack</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex justify-between items-center p-4 border rounded" 
                             :class="isPacked({{ $item->id }}) ? 'bg-green-100 border-green-500' : 'bg-gray-50'">
                            <div>
                                <p class="font-bold text-lg">{{ $item->product_name }}</p>
                                <p class="text-sm text-gray-600">SKU: {{ $item->sku }}</p>
                                <p class="mt-1 text-xs text-gray-500" x-text="scanRangeLabel({{ $item->id }})"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold" x-text="progressLabel({{ $item->id }})"></p>
                                <span x-show="isPacked({{ $item->id }})" class="text-green-600 font-bold">READY</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Section -->
                <div class="w-full md:w-1/3 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between">
                    <div>
                        <h4 class="text-xl font-bold mb-4">Status</h4>
                        <div class="mb-4">
                            <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
                            <p><strong>Waybill:</strong> {{ $order->waybill_number ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="mb-4 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700" x-text="statusText"></div>

                        <div class="p-4 rounded text-center mb-6 transition-colors" :class="allPacked ? 'bg-green-500 text-white' : 'bg-yellow-100 text-yellow-800'">
                            <span x-show="!allPacked" class="font-bold text-xl">Scanning...</span>
                            <span x-show="allPacked" class="font-bold text-xl">ALL ITEMS PACKED!</span>
                        </div>
                    </div>

                    <form action="{{ route('orders.packing.mark-packed', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" :disabled="!allPacked" class="w-full bg-indigo-600 text-white font-bold py-4 px-6 rounded text-xl shadow hover:bg-indigo-800 disabled:opacity-50 disabled:cursor-not-allowed">
                            Complete Packing
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function packer() {
            return {
                scanInput: '',
                currentStatus: @json((string) ($order->delivery_status ?? 'waybill_printed')),
                scanning: false,
                items: @json($order->items->map(function($item){
                    $units = $item->inventoryUnits
                        ->where('status', \App\Models\InventoryUnit::STATUS_ALLOCATED)
                        ->sortBy('id')
                        ->values();
                    $scannedCodes = $units
                        ->filter(fn ($unit) => !empty($unit->packed_scan_at))
                        ->pluck('unit_code')
                        ->filter()
                        ->values();

                    return [
                        'id' => $item->id,
                        'sku' => $item->sku,
                        'qty' => (int) $item->quantity,
                        'scanned_count' => $scannedCodes->count(),
                        'scanned_codes' => $scannedCodes->all(),
                    ];
                })),

                get statusText() {
                    const map = {
                        waybill_printed: 'Current step: Waybill Printed. Start scanning to move this order into Picked From Rack.',
                        picked_from_rack: 'Current step: Picked From Rack. Complete all scans, then mark the order as Packed.',
                        packed: 'Current step: Packed. Return to the queue and mark it as Dispatched.',
                    };

                    return map[this.currentStatus] || 'Current step: Processing fulfillment.';
                },

                notify(type, message) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: type,
                            text: message,
                            timer: 2200,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                        return;
                    }
                    alert(message);
                },

                async scanItem() {
                    const sku = this.scanInput.trim();
                    if (!sku || this.scanning) return;

                    this.scanning = true;

                    try {
                        const response = await fetch(@json(route('orders.packing.scan', $order->id)), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token()),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ unit_code: sku }),
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok || !data.success) {
                            this.notify('error', data.message || 'Failed to scan item.');
                            return;
                        }

                        this.currentStatus = data.delivery_status || this.currentStatus;

                        const item = this.items.find((entry) => Number(entry.id) === Number(data.order_item_id));
                        if (item) {
                            item.scanned_count = Number(data.scanned_count || item.scanned_count || 0);
                            if (!Array.isArray(item.scanned_codes)) {
                                item.scanned_codes = [];
                            }
                            if (data.unit_code && !item.scanned_codes.includes(data.unit_code)) {
                                item.scanned_codes.push(data.unit_code);
                            }
                        }

                        this.notify('success', `${data.unit_code || sku} matched.`);
                    } catch (error) {
                        this.notify('error', 'Failed to scan item.');
                    } finally {
                        this.scanning = false;
                    }
                    
                    this.scanInput = '';
                    this.$nextTick(() => this.$refs.scanInput?.focus());
                },
                
                progressLabel(itemId) {
                    const item = this.items.find((entry) => Number(entry.id) === Number(itemId));
                    if (!item) {
                        return '0 / 0';
                    }

                    return `${item.scanned_count || 0} / ${item.qty || 0}`;
                },

                isPacked(itemId) {
                    const item = this.items.find((entry) => Number(entry.id) === Number(itemId));
                    return !!item && Number(item.scanned_count || 0) >= Number(item.qty || 0);
                },

                scanRangeLabel(itemId) {
                    const item = this.items.find((entry) => Number(entry.id) === Number(itemId));
                    const codes = Array.isArray(item?.scanned_codes) ? item.scanned_codes : [];

                    if (codes.length === 0) {
                        return 'No unit labels scanned yet.';
                    }

                    const first = codes[0];
                    const last = codes[codes.length - 1];

                    return codes.length === 1 || first === last
                        ? `Scanned label: ${first}`
                        : `Scanned labels: ${first} to ${last}`;
                },

                get allPacked() {
                    return this.items.length > 0 && this.items.every((item) => Number(item.scanned_count || 0) >= Number(item.qty || 0));
                }
            }
        }
    </script>
</x-app-layout>
