<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\InventoryUnit;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StoreRack;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseStorePlacementTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_creation_does_not_create_inventory_or_stock(): void
    {
        $user = User::factory()->create();
        [$supplier, $variant] = $this->purchaseDependencies();

        $response = $this->actingAs($user)->post(route('purchases.store'), [
            'supplier_id' => $supplier->id,
            'purchase_number' => 'PUR-NO-AUTO-STOCK',
            'items' => [
                [
                    'product_variant_id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'product_name' => 'Manual Store Product',
                    'quantity' => 4,
                    'purchase_price' => 100,
                ],
            ],
            'discount_type' => 'fixed',
            'discount_value' => 0,
        ]);

        $purchase = Purchase::where('purchase_number', 'PUR-NO-AUTO-STOCK')->firstOrFail();

        $response->assertRedirect(route('purchases.success', $purchase));
        $this->assertSame(0, InventoryUnit::where('purchase_id', $purchase->id)->count());
        $this->assertSame(0, (int) $variant->fresh()->quantity);
    }

    public function test_verified_purchase_item_can_be_added_to_store_rack_manually(): void
    {
        $user = User::factory()->create();
        [, $variant, $purchase, $item] = $this->verifiedPurchaseItem(5);
        $rack = StoreRack::create([
            'store_type' => StoreRack::STORE_RETAIL,
            'rack_name' => 'Rack A',
            'rack_key' => 'rack a',
            'row_name' => 'Row A',
            'row_key' => 'row a',
        ]);

        $response = $this->actingAs($user)->post(route('purchases.store-placement.store', 'retail'), [
            'purchase_item_id' => $item->id,
            'store_rack_id' => $rack->id,
            'quantity' => 3,
        ]);

        $response->assertRedirect();
        $this->assertSame(3, InventoryUnit::where('purchase_item_id', $item->id)->where('status', InventoryUnit::STATUS_AVAILABLE)->count());
        $this->assertSame(3, InventoryUnit::where('purchase_item_id', $item->id)->where('store_type', StoreRack::STORE_RETAIL)->where('store_rack_id', $rack->id)->count());
        $this->assertSame(3, (int) $variant->fresh()->quantity);
        $this->assertSame('verified', $purchase->fresh()->status);
    }

    public function test_scanning_purchase_sku_adds_one_unit_to_selected_store_rack(): void
    {
        $user = User::factory()->create();
        [, $variant, $purchase, $item] = $this->verifiedPurchaseItem(2);
        $rack = StoreRack::create([
            'store_type' => StoreRack::STORE_RETAIL,
            'rack_name' => 'Scan Rack A',
            'rack_key' => 'scan rack a',
            'row_name' => 'Scan Row A',
            'row_key' => 'scan row a',
        ]);

        $response = $this->actingAs($user)->postJson(route('purchases.store-placement.scan', 'retail'), [
            'store_rack_id' => $rack->id,
            'barcode' => $variant->sku,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'rack' => 'Scan Rack A / Scan Row A',
                'placed_count' => 1,
                'remaining_count' => 1,
                'purchase_status' => 'verified',
            ]);
        $this->assertSame(1, InventoryUnit::where('purchase_item_id', $item->id)->where('store_rack_id', $rack->id)->count());
        $this->assertSame(1, (int) $variant->fresh()->quantity);
        $this->assertSame('verified', $purchase->fresh()->status);
    }

    public function test_scanning_same_sku_repeatedly_stops_after_remaining_purchase_quantity(): void
    {
        $user = User::factory()->create();
        [, $variant, $purchase, $item] = $this->verifiedPurchaseItem(2);
        $rack = StoreRack::create([
            'store_type' => StoreRack::STORE_WAREHOUSE,
            'rack_name' => 'Scan Rack B',
            'rack_key' => 'scan rack b',
            'row_name' => 'Scan Row B',
            'row_key' => 'scan row b',
        ]);

        foreach (range(1, 2) as $scan) {
            $this->actingAs($user)->postJson(route('purchases.store-placement.scan', 'warehouse'), [
                'store_rack_id' => $rack->id,
                'barcode' => $variant->sku,
            ])->assertOk();
        }

        $this->actingAs($user)->postJson(route('purchases.store-placement.scan', 'warehouse'), [
            'store_rack_id' => $rack->id,
            'barcode' => $variant->sku,
        ])->assertStatus(422);

        $this->assertSame(2, InventoryUnit::where('purchase_item_id', $item->id)->where('store_rack_id', $rack->id)->count());
        $this->assertSame(2, (int) $variant->fresh()->quantity);
        $this->assertSame('complete', $purchase->fresh()->status);
    }

    public function test_scanning_rejects_rack_from_other_store(): void
    {
        $user = User::factory()->create();
        [, $variant] = $this->verifiedPurchaseItem(1);
        $warehouseRack = StoreRack::create([
            'store_type' => StoreRack::STORE_WAREHOUSE,
            'rack_name' => 'Wrong Store Rack',
            'rack_key' => 'wrong store rack',
            'row_name' => 'Wrong Store Row',
            'row_key' => 'wrong store row',
        ]);

        $response = $this->actingAs($user)->postJson(route('purchases.store-placement.scan', 'retail'), [
            'store_rack_id' => $warehouseRack->id,
            'barcode' => $variant->sku,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_store_placement_cannot_exceed_remaining_purchase_item_quantity(): void
    {
        $user = User::factory()->create();
        [, $variant, , $item] = $this->verifiedPurchaseItem(2);
        $rack = StoreRack::create([
            'store_type' => StoreRack::STORE_WAREHOUSE,
            'rack_name' => 'Rack B',
            'rack_key' => 'rack b',
            'row_name' => 'Row B',
            'row_key' => 'row b',
        ]);

        $response = $this->actingAs($user)
            ->from(route('purchases.store-placement.index', 'warehouse'))
            ->post(route('purchases.store-placement.store', 'warehouse'), [
                'purchase_item_id' => $item->id,
                'store_rack_id' => $rack->id,
                'quantity' => 3,
            ]);

        $response->assertRedirect(route('purchases.store-placement.index', 'warehouse'));
        $response->assertSessionHasErrors('quantity');
        $this->assertSame(0, InventoryUnit::where('purchase_item_id', $item->id)->count());
        $this->assertSame(0, (int) $variant->fresh()->quantity);
    }

    public function test_purchase_completes_when_all_items_are_placed_into_store_stock(): void
    {
        $user = User::factory()->create();
        [, , $purchase, $item] = $this->verifiedPurchaseItem(2);
        $rack = StoreRack::create([
            'store_type' => StoreRack::STORE_WAREHOUSE,
            'rack_name' => 'Rack C',
            'rack_key' => 'rack c',
            'row_name' => 'Row C',
            'row_key' => 'row c',
        ]);

        $this->actingAs($user)->post(route('purchases.store-placement.store', 'warehouse'), [
            'purchase_item_id' => $item->id,
            'store_rack_id' => $rack->id,
            'quantity' => 2,
        ])->assertRedirect();

        $purchase->refresh();
        $this->assertSame('complete', $purchase->status);
        $this->assertNotNull($purchase->completed_at);
        $this->assertNotNull($purchase->stock_applied_at);
    }

    public function test_store_placement_and_rack_pages_render_for_each_store(): void
    {
        $user = User::factory()->create();
        $this->verifiedPurchaseItem(1);

        foreach ([StoreRack::STORE_RETAIL, StoreRack::STORE_WAREHOUSE] as $store) {
            StoreRack::create([
                'store_type' => $store,
                'rack_name' => StoreRack::storeLabel($store).' Rack',
                'rack_key' => StoreRack::normalizeRackKey(StoreRack::storeLabel($store).' Rack'),
                'row_name' => StoreRack::storeLabel($store).' Row',
                'row_key' => StoreRack::normalizeRowKey(StoreRack::storeLabel($store).' Row'),
            ]);

            $this->actingAs($user)
                ->get(route('purchases.store-racks.index', $store))
                ->assertOk()
                ->assertSee(StoreRack::storeLabel($store).' Racks')
                ->assertSee(StoreRack::storeLabel($store).' Rack')
                ->assertSee(StoreRack::storeLabel($store).' Row');

            $this->actingAs($user)
                ->get(route('purchases.store-placement.index', $store))
                ->assertOk()
                ->assertSee('Add to '.StoreRack::storeLabel($store))
                ->assertSee(StoreRack::storeLabel($store).' Rack')
                ->assertSee(StoreRack::storeLabel($store).' Row')
                ->assertSee('Search and select rack row')
                ->assertSee('Use Rack');
        }
    }

    public function test_store_rack_creation_uses_separate_rack_and_row_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('purchases.store-racks.store', 'retail'), [
            'rack_name' => 'Rack A',
            'row_name' => 'Row 1',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('purchases.store-racks.store', 'retail'), [
            'rack_name' => 'Rack B',
            'row_name' => 'Row 1',
        ])->assertRedirect();

        $this->assertDatabaseHas('store_racks', [
            'store_type' => StoreRack::STORE_RETAIL,
            'rack_name' => 'Rack A',
            'row_name' => 'Row 1',
        ]);
        $this->assertDatabaseHas('store_racks', [
            'store_type' => StoreRack::STORE_RETAIL,
            'rack_name' => 'Rack B',
            'row_name' => 'Row 1',
        ]);
    }

    public function test_store_rack_creation_rejects_duplicate_rack_and_row_inside_same_store(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('purchases.store-racks.store', 'warehouse'), [
            'rack_name' => 'Rack A',
            'row_name' => 'Row 1',
        ])->assertRedirect();

        $this->actingAs($user)
            ->from(route('purchases.store-racks.index', 'warehouse'))
            ->post(route('purchases.store-racks.store', 'warehouse'), [
                'rack_name' => ' rack   a ',
                'row_name' => ' row   1 ',
            ])
            ->assertRedirect(route('purchases.store-racks.index', 'warehouse'))
            ->assertSessionHasErrors('row_name');
    }

    private function purchaseDependencies(): array
    {
        $supplier = Supplier::create([
            'name' => 'Store Supplier',
            'business_name' => 'Store Supplier Business',
            'mobile' => '0770000000',
        ]);
        $category = Category::create(['name' => 'Store Category', 'code' => 'STORE']);
        $unit = Unit::create(['name' => 'Piece', 'short_name' => 'pcs']);
        $product = Product::create([
            'name' => 'Manual Store Product',
            'category_id' => $category->id,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'unit_id' => $unit->id,
            'sku' => 'SKU-MANUAL-STORE',
            'selling_price' => 200,
            'limit_price' => 100,
            'quantity' => 0,
            'alert_quantity' => 0,
        ]);

        return [$supplier, $variant];
    }

    private function verifiedPurchaseItem(int $quantity): array
    {
        [$supplier, $variant] = $this->purchaseDependencies();
        $purchase = Purchase::create([
            'purchase_number' => 'PUR-MANUAL-STORE-'.$quantity.'-'.uniqid(),
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'status' => 'verified',
            'currency' => 'LKR',
            'sub_total' => 100 * $quantity,
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'discount_amount' => 0,
            'net_total' => 100 * $quantity,
            'paid_amount' => 0,
        ]);
        $item = PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $variant->product_id,
            'stock_variant_id' => $variant->id,
            'product_name' => 'Manual Store Product',
            'quantity' => $quantity,
            'purchase_price' => 100,
            'total' => 100 * $quantity,
        ]);

        return [$supplier, $variant, $purchase, $item];
    }
}
