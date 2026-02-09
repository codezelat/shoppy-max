<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\Unit;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class OrderUpdatePerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_update_performance()
    {
        // 1. Setup Data
        $user = User::factory()->create();

        $category = Category::create(['name' => 'Test Category']);
        $unit = Unit::create(['name' => 'Test Unit', 'short_name' => 'tu']);

        $product = Product::create([
            'name' => 'Test Product',
            'sku' => 'TP-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'selling_price' => 100,
            'quantity' => 1000,
            'alert_quantity' => 10,
        ]);

        $variants = [];
        for ($i = 0; $i < 10; $i++) {
            $variants[] = ProductVariant::create([
                'product_id' => $product->id,
                'unit_id' => $unit->id,
                'sku' => 'TP-001-V' . $i,
                'selling_price' => 100,
                'limit_price' => 80,
                'quantity' => 100, // Initial quantity
                'alert_quantity' => 10,
            ]);
        }

        $customer = Customer::create([
            'name' => 'Test Customer',
            'mobile' => '1234567890',
            'address' => 'Test Address',
            'city' => 'Test City',
            'country' => 'Sri Lanka',
        ]);

        $order = Order::forceCreate([
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'order_date' => now(),
            'customer_name' => 'Test Customer',
            'customer_phone' => '1234567890',
            'customer_address' => 'Test Address',
            'status' => 'pending',
            'order_type' => 'direct',
            'total_amount' => 0,
        ]);

        // 2. Prepare Payload
        $items = [];
        foreach ($variants as $variant) {
            $items[] = [
                'id' => $variant->id,
                'quantity' => 2,
                'selling_price' => 100,
            ];
        }

        $payload = [
            'order_type' => 'direct',
            'order_date' => now()->format('Y-m-d'),
            'customer' => [
                'name' => 'Test Customer',
                'mobile' => '1234567890',
                'address' => 'Test Address',
                'city' => 'Test City',
            ],
            'items' => $items,
            'payment_method' => 'cod',
            'call_status' => 'pending',
        ];

        // 3. Measure Queries
        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($user)->putJson(route('orders.update', $order->id), $payload);

        $response->assertStatus(200);

        $queryLog = DB::getQueryLog();
        $queryCount = count($queryLog);


        // Assert query count is significantly reduced (e.g. < 45)
        // Baseline was 66.
        $this->assertLessThan(45, $queryCount, "Query count should be optimized (was 66, expected < 45)");

        // Assert stock decrement
        foreach ($variants as $variant) {
            $this->assertDatabaseHas('product_variants', [
                'id' => $variant->id,
                'quantity' => 98, // 100 - 2
            ]);
        }
    }
}
