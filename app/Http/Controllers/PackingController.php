<?php

namespace App\Http\Controllers;

use App\Models\InventoryUnit;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\StoreRack;
use App\Services\InventoryUnitService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PackingController extends Controller
{
    private const STAGES = [
        'ready' => [
            'status' => 'waybill_printed',
            'title' => 'Ready To Pick',
            'description' => 'Waybill printed orders waiting for rack picking.',
            'empty' => 'No waybill printed orders are waiting to be picked.',
        ],
        'picking' => [
            'status' => 'picked_from_rack',
            'title' => 'Picking',
            'description' => 'Orders currently being scanned from rack labels.',
            'empty' => 'No orders are currently in picking.',
        ],
        'packed' => [
            'status' => 'packed',
            'title' => 'Packed',
            'description' => 'Orders fully scanned and ready to dispatch.',
            'empty' => 'No packed orders are waiting for dispatch.',
        ],
    ];

    public function __construct(
        private readonly InventoryUnitService $inventoryUnits
    ) {}

    /**
     * List orders for packing.
     */
    public function index(Request $request)
    {
        return redirect()->route('orders.packing.ready', $request->query());
    }

    public function ready(Request $request)
    {
        return $this->queue($request, 'ready');
    }

    public function picking(Request $request)
    {
        return $this->queue($request, 'picking');
    }

    public function packed(Request $request)
    {
        return $this->queue($request, 'packed');
    }

    private function queue(Request $request, string $stage)
    {
        $perPage = in_array((int) $request->input('per_page'), [15, 25, 50, 100], true)
            ? (int) $request->input('per_page')
            : 25;
        $stageConfig = self::STAGES[$stage] ?? self::STAGES['ready'];

        $orders = $this->buildPackingQueueQuery($request, $stageConfig['status'])
            ->with(['customer', 'courier', 'items.inventoryUnits.purchase', 'items.inventoryUnits.storeRack'])
            ->orderBy('waybill_printed_at')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        $orders->setCollection(
            $orders->getCollection()->map(function (Order $order) {
                $order->packing_summary = $this->buildPackingSummary($order);

                return $order;
            })
        );

        $statsBaseQuery = Order::query()
            ->where('call_status', 'confirm')
            ->whereIn('delivery_status', ['waybill_printed', 'picked_from_rack', 'packed']);

        $stats = [
            'total' => (clone $statsBaseQuery)->count(),
            'waybill_printed' => (clone $statsBaseQuery)->where('delivery_status', 'waybill_printed')->count(),
            'picked_from_rack' => (clone $statsBaseQuery)->where('delivery_status', 'picked_from_rack')->count(),
            'packed' => (clone $statsBaseQuery)->where('delivery_status', 'packed')->count(),
        ];

        $filters = [
            'search' => trim((string) $request->input('search', '')),
            'per_page' => $perPage,
        ];
        $stageRoutes = [
            'ready' => route('orders.packing.ready', request()->except(['page'])),
            'picking' => route('orders.packing.picking', request()->except(['page'])),
            'packed' => route('orders.packing.packed', request()->except(['page'])),
        ];

        return view('orders.packing.index', compact('orders', 'stats', 'filters', 'stage', 'stageConfig', 'stageRoutes'));
    }

    /**
     * Packing Interface for a specific order (Scanner UI).
     */
    public function process($id)
    {
        $order = Order::with(['items.inventoryUnits' => function ($query) {
            $query->where(function ($unitQuery) {
                $unitQuery->where('status', InventoryUnit::STATUS_ALLOCATED)
                    ->orWhereNotNull('packed_scan_at');
            })
                ->with(['purchase', 'storeRack'])
                ->orderBy('id');
        }])->findOrFail($id);

        if ((string) ($order->call_status ?? '') !== 'confirm' || ! in_array((string) ($order->delivery_status ?? ''), ['waybill_printed', 'picked_from_rack'], true)) {
            return redirect()->route('orders.packing.ready')->with('error', 'Only call-confirmed orders in the picking queue can be packed.');
        }

        $packingSummary = $this->buildPackingSummary($order);

        return view('orders.packing.process', compact('order', 'packingSummary'));
    }

    public function scan(Request $request, $id)
    {
        $validated = $request->validate([
            'unit_code' => 'required|string|max:255',
        ]);

        $order = Order::with(['items.inventoryUnits'])->findOrFail($id);

        if ((string) ($order->call_status ?? '') !== 'confirm' || ! in_array((string) ($order->delivery_status ?? ''), ['waybill_printed', 'picked_from_rack'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Only call-confirmed orders in the picking queue can be scanned.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            if ((string) ($order->delivery_status ?? '') === 'waybill_printed') {
                $order->delivery_status = 'picked_from_rack';
                if (! $order->picked_at) {
                    $order->picked_at = now();
                }
                if (! $order->picked_by) {
                    $order->picked_by = Auth::id();
                }
                $order->save();

                OrderLog::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'action' => 'picked_from_rack',
                    'description' => 'Order moved to picked from rack.',
                ]);
            }

            $result = $this->inventoryUnits->scanOrderUnitForPacking($order, $validated['unit_code'], Auth::id());
            $summary = $this->buildPackingSummary($order->fresh(['items.inventoryUnits.purchase', 'items.inventoryUnits.storeRack']));
            $autoPacked = false;

            if (($summary['all_scanned'] ?? false) && (string) ($order->delivery_status ?? '') === 'picked_from_rack') {
                $this->markOrderPacked($order, Auth::id());
                $order->refresh();
                $autoPacked = true;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $autoPacked ? 'All labels scanned. Order packed.' : 'Item scanned successfully.',
                'delivery_status' => $order->delivery_status,
                'auto_packed' => $autoPacked,
                'order_item_id' => $result['order_item_id'],
                'unit_code' => $result['unit']->unit_code,
                'scanned_count' => $result['scanned_count'],
                'required_count' => $result['required_count'],
                'summary' => $summary,
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?: 'Unable to scan item.',
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while scanning.',
            ], 422);
        }
    }

    public function markPicked(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ((string) ($order->call_status ?? '') !== 'confirm' || (string) ($order->delivery_status ?? '') !== 'waybill_printed') {
            return response()->json([
                'success' => false,
                'message' => 'Only call-confirmed waybill-printed orders can move to Picked From Rack.',
            ], 422);
        }

        $order->delivery_status = 'picked_from_rack';
        $order->status = $order->call_status === 'hold' ? 'hold' : 'confirm';
        if (! $order->picked_at) {
            $order->picked_at = now();
        }
        if (! $order->picked_by) {
            $order->picked_by = Auth::id();
        }
        $order->save();

        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'action' => 'picked_from_rack',
            'description' => 'Order moved to picked from rack.',
        ]);

        return response()->json([
            'success' => true,
            'delivery_status' => $order->delivery_status,
        ]);
    }

    /**
     * Mark as packed.
     */
    public function markPacked(Request $request, $id)
    {
        $order = Order::with(['items.inventoryUnits'])->findOrFail($id);

        if ((string) ($order->call_status ?? '') !== 'confirm' || (string) ($order->delivery_status ?? '') !== 'picked_from_rack') {
            return redirect()->route('orders.packing.picking')->with('error', 'Only picked orders can be marked as packed.');
        }

        foreach ($this->buildPackingSummary($order)['items'] as $itemSummary) {
            if (($itemSummary['scanned_count'] ?? 0) < ($itemSummary['required_count'] ?? 0)) {
                return redirect()->route('orders.packing.process', $order->id)
                    ->with('error', 'All allocated unit labels must be scanned before completing packing.');
            }
        }

        $this->markOrderPacked($order, Auth::id());

        return redirect()->route('orders.packing.packed')->with('success', 'Order packed successfully.');
    }

    public function markDispatched(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ((string) ($order->call_status ?? '') !== 'confirm' || (string) ($order->delivery_status ?? '') !== 'packed') {
            return redirect()->route('orders.packing.packed')->with('error', 'Only packed orders can be marked as dispatched.');
        }

        $order->delivery_status = 'dispatched';
        $order->status = $order->call_status === 'hold' ? 'hold' : 'confirm';
        if (! $order->dispatched_at) {
            $order->dispatched_at = now();
        }
        if (! $order->dispatched_by) {
            $order->dispatched_by = Auth::id();
        }
        $order->save();

        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'action' => 'marked_dispatched',
            'description' => 'Order marked as dispatched after packing.',
        ]);

        return redirect()->route('orders.packing.packed')->with('success', 'Order marked as dispatched.');
    }

    /**
     * Create Packing Batch (Placeholder).
     */
    public function createBatch(Request $request)
    {
        // Batch logic to be implemented
        return back()->with('info', 'Batch creation feature coming soon.');
    }

    private function buildPackingSummary(Order $order): array
    {
        $items = $order->items->map(function ($item) {
            $units = $item->inventoryUnits
                ->where('status', InventoryUnit::STATUS_ALLOCATED)
                ->sortBy(fn ($unit) => sprintf(
                    '%02d|%s|%010d',
                    $this->storePriority((string) ($unit->store_type ?? '')),
                    $unit->storeRack?->display_label ?? '',
                    (int) $unit->id
                ))
                ->values();

            $scannedUnits = $units->filter(fn ($unit) => ! empty($unit->packed_scan_at))->values();

            return [
                'order_item_id' => $item->id,
                'sku' => $item->sku,
                'product_name' => $item->product_name,
                'required_count' => max((int) ($item->quantity ?? 0), 0),
                'scanned_count' => $scannedUnits->count(),
                'scanned_codes' => $scannedUnits->pluck('unit_code')->filter()->values()->all(),
                'units' => $units->map(fn ($unit) => [
                    'id' => $unit->id,
                    'unit_code' => $unit->unit_code,
                    'store_type' => $unit->store_type,
                    'store_label' => $unit->store_type ? StoreRack::storeLabel((string) $unit->store_type) : 'Unassigned Store',
                    'rack_label' => $unit->storeRack?->display_label ?? 'Unassigned Rack',
                    'purchase_number' => $unit->purchase?->purchase_number ?? 'Legacy stock',
                    'scanned' => ! empty($unit->packed_scan_at),
                ])->values()->all(),
            ];
        })->values();

        return [
            'items' => $items->all(),
            'all_scanned' => $items->every(fn ($item) => ($item['required_count'] ?? 0) > 0 && ($item['scanned_count'] ?? 0) >= ($item['required_count'] ?? 0)),
        ];
    }

    private function buildPackingQueueQuery(Request $request, string $deliveryStatus): Builder
    {
        $query = Order::query()
            ->where('call_status', 'confirm')
            ->where('delivery_status', $deliveryStatus);

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function (Builder $searchQuery) use ($search) {
                $searchQuery
                    ->where('order_number', 'like', "%{$search}%")
                    ->orWhere('waybill_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhereHas('customer', function (Builder $customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items', function (Builder $itemQuery) use ($search) {
                        $itemQuery->where('product_name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhereHas('inventoryUnits', function (Builder $unitQuery) use ($search) {
                                $unitQuery->where('unit_code', 'like', "%{$search}%")
                                    ->orWhereHas('purchase', fn (Builder $purchaseQuery) => $purchaseQuery->where('purchase_number', 'like', "%{$search}%"))
                                    ->orWhereHas('storeRack', function (Builder $rackQuery) use ($search) {
                                        $rackQuery->where('rack_name', 'like', "%{$search}%")
                                            ->orWhere('row_name', 'like', "%{$search}%");
                                    });
                            });
                    });
            });
        }

        return $query;
    }

    private function markOrderPacked(Order $order, ?int $userId): void
    {
        if ((string) ($order->delivery_status ?? '') === 'packed') {
            return;
        }

        $order->packed_by = $userId;
        $order->delivery_status = 'packed';
        $order->status = $order->call_status === 'hold' ? 'hold' : 'confirm';
        if (! $order->packed_at) {
            $order->packed_at = now();
        }
        $order->save();

        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => $userId,
            'action' => 'packed_confirm',
            'description' => 'Order packed after all allocated labels were scanned.',
        ]);
    }

    private function storePriority(string $storeType): int
    {
        return match (StoreRack::normalizeStoreType($storeType)) {
            StoreRack::STORE_RETAIL => 0,
            StoreRack::STORE_WAREHOUSE => 1,
            default => 2,
        };
    }
}
