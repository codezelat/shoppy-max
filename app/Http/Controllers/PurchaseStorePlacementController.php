<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use App\Models\StoreRack;
use App\Services\InventoryUnitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'store_rack_id' => 'required|exists:store_racks,id',
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

    private function validatedStore(string $store): string
    {
        $store = StoreRack::normalizeStoreType($store);
        abort_unless(StoreRack::isValidStoreType($store), 404);

        return $store;
    }
}
