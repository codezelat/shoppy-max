<?php

namespace Tests\Feature;

use App\Http\Controllers\ProductController;
use App\Models\Category;
use App\Models\InventoryUnit;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_create_rejects_duplicate_exact_variant_units(): void
    {
        $user = User::factory()->create();
        [$category, $unit] = $this->productDependencies();

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Duplicate Unit Product',
            'category_id' => $category->id,
            'variants' => [
                $this->variantPayload($unit, ' 500 ', 'SKU-DUP-001'),
                $this->variantPayload($unit, '500', 'SKU-DUP-002'),
            ],
        ]);

        $response->assertSessionHasErrors(['variants.1.unit_id']);
        $this->assertDatabaseMissing('products', ['name' => 'Duplicate Unit Product']);
    }

    public function test_product_create_allows_same_unit_with_different_values(): void
    {
        $user = User::factory()->create();
        [$category, $unit] = $this->productDependencies();

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Different Unit Values Product',
            'category_id' => $category->id,
            'variants' => [
                $this->variantPayload($unit, '500', 'SKU-UNIT-500'),
                $this->variantPayload($unit, '1000', 'SKU-UNIT-1000'),
            ],
        ]);

        $product = Product::where('name', 'Different Unit Values Product')->firstOrFail();

        $response->assertRedirect(route('products.success', $product));
        $this->assertSame(2, $product->variants()->count());
    }

    public function test_product_update_rejects_duplicate_exact_variant_units(): void
    {
        $user = User::factory()->create();
        [$category, $unit] = $this->productDependencies();
        $product = Product::create([
            'name' => 'Existing Product',
            'category_id' => $category->id,
        ]);
        $firstVariant = ProductVariant::create($this->storedVariantPayload($product, $unit, '500', 'SKU-EXISTING-500'));
        $secondVariant = ProductVariant::create($this->storedVariantPayload($product, $unit, '1000', 'SKU-EXISTING-1000'));

        $response = $this->actingAs($user)->put(route('products.update', $product), [
            'name' => $product->name,
            'category_id' => $category->id,
            'variants' => [
                ['id' => $firstVariant->id] + $this->variantPayload($unit, '500', $firstVariant->sku),
                ['id' => $secondVariant->id] + $this->variantPayload($unit, ' 500 ', $secondVariant->sku),
            ],
        ]);

        $response->assertSessionHasErrors(['variants.1.unit_id']);
        $this->assertSame('1000', $secondVariant->fresh()->unit_value);
    }

    public function test_quantity_product_barcode_labels_repeat_variant_sku_not_internal_unit_codes(): void
    {
        [$category, $unit] = $this->productDependencies();
        $product = Product::create([
            'name' => 'Barcode Product',
            'category_id' => $category->id,
        ]);
        $variant = ProductVariant::create(array_merge(
            $this->storedVariantPayload($product, $unit, '500', 'SKU-PRODUCT-500'),
            ['quantity' => 2]
        ));

        InventoryUnit::create([
            'product_variant_id' => $variant->id,
            'unit_code' => 'IU-PRODUCT-0001',
            'status' => InventoryUnit::STATUS_AVAILABLE,
            'sku_snapshot' => $variant->sku,
            'available_at' => now(),
            'last_event_at' => now(),
        ]);
        InventoryUnit::create([
            'product_variant_id' => $variant->id,
            'unit_code' => 'IU-PRODUCT-0002',
            'status' => InventoryUnit::STATUS_AVAILABLE,
            'sku_snapshot' => $variant->sku,
            'available_at' => now(),
            'last_event_at' => now(),
        ]);

        $method = new \ReflectionMethod(ProductController::class, 'buildBarcodeLabelsForVariant');
        $method->setAccessible(true);

        $labels = $method->invoke(app(ProductController::class), $variant);

        $this->assertCount(2, $labels);
        $this->assertSame(['SKU-PRODUCT-500', 'SKU-PRODUCT-500'], $labels->pluck('barcode_value')->all());
        $this->assertSame(['SKU-PRODUCT-500', 'SKU-PRODUCT-500'], $labels->pluck('display_code')->all());
        $this->assertNotContains('IU-PRODUCT-0001', $labels->pluck('barcode_value')->all());
        $this->assertNotContains('IU-PRODUCT-0002', $labels->pluck('display_code')->all());
    }

    private function productDependencies(): array
    {
        return [
            Category::create(['name' => 'Beauty', 'code' => 'BEAUTY']),
            Unit::create(['name' => 'Gram', 'short_name' => 'g']),
        ];
    }

    private function variantPayload(Unit $unit, ?string $unitValue, string $sku): array
    {
        return [
            'unit_id' => $unit->id,
            'unit_value' => $unitValue,
            'sku' => $sku,
            'selling_price' => 100,
            'limit_price' => 80,
            'alert_quantity' => 0,
        ];
    }

    private function storedVariantPayload(Product $product, Unit $unit, ?string $unitValue, string $sku): array
    {
        return [
            'product_id' => $product->id,
            'unit_id' => $unit->id,
            'unit_value' => $unitValue,
            'sku' => $sku,
            'selling_price' => 100,
            'limit_price' => 80,
            'quantity' => 0,
            'alert_quantity' => 0,
        ];
    }
}
