<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use App\Models\StoreRack;
use App\Services\InventoryUnitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PurchaseStorePlacementController extends Controller
{
    public function __construct(private readonly InventoryUnitService $inventoryUnits) {}

    public function index(Request $request, string $store)
    {
        $store = $this->validatedStore($store);
        $search = trim((string) $request->query('search', ''));

        $query = PurchaseItem::query()
            ->with(['purchase.supplier', 'variant.product', 'variant.unit', 'inventoryUnits'])
            ->whereHas('purchase', fn ($purchaseQuery) => $purchaseQuery->whereIn('status', ['verified', 'complete']))
            ->latest('id');

        if ($search !== '') {
            $query->where(function ($itemQuery) use ($search) {
                $itemQuery->where('product_name', 'like', "%{$search}%")
                    ->orWhereHas('purchase', fn ($purchaseQuery) => $purchaseQuery->where('purchase_number', 'like', "%{$search}%"))
                    ->orWhereHas('variant', fn ($variantQuery) => $variantQuery->where('sku', 'like', "%{$search}%"));
            });
        }

        $items = $query->paginate(15)->withQueryString();
        $racks = StoreRack::query()
            ->where('store_type', $store)
            ->orderBy('rack_key')
            ->orderBy('row_key')
            ->get();

        return view('purchases.store-placement.index', [
            'items' => $items,
            'racks' => $racks,
            'store' => $store,
            'storeLabel' => StoreRack::storeLabel($store),
            'search' => $search,
        ]);
    }

    public function store(Request $request, string $store)
    {
        $store = $this->validatedStore($store);

        $validated = $request->validate([
            'purchase_item_id' => 'required|exists:purchase_items,id',
            'store_rack_id' => [
                'required',
                Rule::exists('store_racks', 'id')->where('store_type', $store),
            ],
            'quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $rack = StoreRack::query()
                ->where('store_type', $store)
                ->findOrFail((int) $validated['store_rack_id']);
            $item = PurchaseItem::query()->findOrFail((int) $validated['purchase_item_id']);

            $placed = $this->inventoryUnits->placePurchaseItemInStore(
                $item,
                $rack,
                (int) $validated['quantity'],
                $request->user()?->id
            );

            DB::commit();

            return back()->with(
                'success',
                number_format($placed->count()).' unit(s) added to '.StoreRack::storeLabel($store).'.'
            );
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Unable to add stock to store: '.$e->getMessage());
        }
    }

    public function scan(Request $request, string $store)
    {
        $store = $this->validatedStore($store);

        try {
            $validated = validator($request->all(), [
                'store_rack_id' => [
                    'required',
                    Rule::exists('store_racks', 'id')->where('store_type', $store),
                ],
                'barcode' => 'required|string|max:120',
            ])->validate();

            DB::beginTransaction();

            $rack = StoreRack::query()
                ->where('store_type', $store)
                ->findOrFail((int) $validated['store_rack_id']);

            $result = $this->inventoryUnits->placeNextPurchaseItemForSkuInStore(
                $rack,
                (string) $validated['barcode'],
                $request->user()?->id
            );

            $item = $result['item'];
            $purchase = $result['purchase'];
            $unit = $result['unit'];

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock added to '.$rack->display_label.'.',
                'purchase_number' => (string) $purchase?->purchase_number,
                'purchase_status' => (string) ($purchase?->status ?? ''),
                'product_name' => (string) $item->product_name,
                'sku' => (string) $item->variant?->sku,
                'rack' => (string) $rack->display_label,
                'unit_code' => (string) $unit?->barcode_value,
                'barcode_value' => (string) $unit?->barcode_value,
                'placed_count' => $item->placedUnitCount(),
                'remaining_count' => $item->remainingPlacementQuantity(),
            ]);
        } catch (ValidationException $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?: 'Unable to add scanned stock.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return response()->json([
                'success' => false,
                'message' => 'Unable to add scanned stock.',
            ], 500);
        }
    }

    private function validatedStore(string $store): string
    {
        $store = StoreRack::normalizeStoreType($store);
        abort_unless(StoreRack::isValidStoreType($store), 404);

        return $store;
    }
}
