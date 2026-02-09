<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ProductImportPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_performance()
    {
        // Setup
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create(['name' => 'Test Cat', 'code' => 'TC']);
        $subCategory = SubCategory::create(['name' => 'Test Sub', 'category_id' => $category->id, 'code' => 'TS']);
        $unit = Unit::create(['name' => 'Test Unit', 'short_name' => 'TU']);

        // Seed some existing data to simulate a real-world scenario where check is necessary
        $existingCount = 50;
        for ($i = 0; $i < $existingCount; $i++) {
            $p = Product::create([
                'name' => "Existing Product $i",
                'category_id' => $category->id,
                'sub_category_id' => $subCategory->id,
                'description' => 'Test',
            ]);
            ProductVariant::create([
                'product_id' => $p->id,
                'unit_id' => $unit->id,
                'sku' => "SKU-$i",
                'selling_price' => 100,
                'quantity' => 10,
                'unit_value' => 1,
            ]);
        }

        // Prepare import data
        $importCount = 500;
        $previewData = [];

        for ($i = 0; $i < $importCount; $i++) {
            // First 50 are duplicates (SKU-0 to SKU-49)
            // Next are new
            $sku = "SKU-$i";

            $previewData[] = [
                'row_id' => $i,
                'name' => "Import Product $i",
                'category_id' => $category->id,
                'sub_category_id' => $subCategory->id,
                'description' => 'Desc',
                'unit_id' => $unit->id,
                'unit_value' => 1,
                'sku' => $sku,
                'selling_price' => 120,
                'limit_price' => 110,
                'quantity' => 20,
                'alert_quantity' => 5,
                'image_url' => null,
                'errors' => []
            ];
        }

        session(['product_import_preview_data' => $previewData]);

        // Enable query logging to count queries
        DB::enableQueryLog();

        $startTime = microtime(true);

        $response = $this->post(route('products.import.store'));

        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        $queryLog = DB::getQueryLog();
        $queryCount = count($queryLog);

        echo "\nPerformance Results:\n";
        echo "Time: " . number_format($duration, 4) . " seconds\n";
        echo "Queries: " . $queryCount . "\n";

        // Assert redirect to confirm no crash
        $response->assertRedirect(route('products.index'));
    }
}
