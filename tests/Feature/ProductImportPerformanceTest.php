<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ProductImportPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_product_import_n_plus_one_optimization()
    {
        // 1. Setup Data
        $user = User::factory()->create();
        $user->assignRole('super admin');

        $category = Category::create(['name' => 'Electronics']);
        $subCategory = SubCategory::create(['name' => 'Phones', 'category_id' => $category->id]);
        $unit = Unit::create(['name' => 'Piece', 'short_name' => 'pc']);

        // Create some existing products to ensure we hit the "find" path
        for ($i = 0; $i < 5; $i++) {
             Product::create([
                'name' => "Existing Product $i",
                'category_id' => $category->id,
                'sub_category_id' => $subCategory->id,
                'description' => 'Existing Description',
                'image' => null,
            ]);
        }

        // 2. Prepare Session Data (Simulation of Excel Import)
        $previewData = [];
        $productCount = 20; // Enough to show the N+1 vs 1

        for ($i = 0; $i < $productCount; $i++) {
            $productName = "Imported Product $i";

            $previewData[] = [
                'row_id' => $i,
                'name' => $productName,
                'category_id' => $category->id,
                'category_name' => $category->name,
                'sub_category_id' => $subCategory->id,
                'sub_category_name' => $subCategory->name,
                'description' => 'Description',
                'unit_id' => $unit->id,
                'unit_name' => $unit->name,
                'unit_value' => 1,
                'sku' => "SKU-$i",
                'selling_price' => 100,
                'limit_price' => 80,
                'quantity' => 10,
                'alert_quantity' => 5,
                'image_url' => null,
                'errors' => []
            ];
        }

        // Add an existing product name to the import list to test the "find" logic
        $existingProduct = Product::first();
        $previewData[] = [
            'row_id' => $productCount,
            'name' => $existingProduct->name,
            'category_id' => $category->id,
            'category_name' => $category->name,
            'sub_category_id' => $subCategory->id,
            'sub_category_name' => $subCategory->name,
            'description' => 'Description',
            'unit_id' => $unit->id,
            'unit_name' => $unit->name,
            'unit_value' => 1,
            'sku' => "SKU-EXISTING",
            'selling_price' => 100,
            'limit_price' => 80,
            'quantity' => 10,
            'alert_quantity' => 5,
            'image_url' => null,
            'errors' => []
        ];

        // Store in session
        session(['product_import_preview_data' => $previewData]);

        // 3. Measure Queries
        DB::enableQueryLog();

        $response = $this->actingAs($user)->post(route('products.import.store'));

        $queries = DB::getQueryLog();

        // Assertions
        $response->assertRedirect(route('products.index'));

        $nPlusOneQueries = collect($queries)->filter(function ($q) {
            return str_contains($q['query'], 'select * from "products" where "name" = ?');
        })->count();

        $optimizedQueries = collect($queries)->filter(function ($q) {
            return str_contains($q['query'], 'select * from "products" where "name" in');
        })->count();

        echo "\nN+1 Queries: " . $nPlusOneQueries;
        echo "\nOptimized Queries: " . $optimizedQueries . "\n";

        $this->assertEquals(0, $nPlusOneQueries, "Should have 0 N+1 queries");
        $this->assertEquals(1, $optimizedQueries, "Should have 1 optimized query");
    }
}
