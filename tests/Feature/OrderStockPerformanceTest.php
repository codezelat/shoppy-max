<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Unit;
use App\Models\Customer;
use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderStockPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_update_stock_revert_performance()
    {
        // 1. Setup Data
        $user = User::factory()->create();
        $this->actingAs($user);

        $customer = Customer::create([
            'name' => 'John Doe',
            'mobile' => '0712345678',
            'address' => '123 Main St',
        ]);

        $courier = Courier::create(['name' => 'Test Courier']);

        $product = Product::create(['name' => 'Test Product']);
        $unit = Unit::create(['name' => 'Test Unit', 'short_name' => 'TU']);

        $variants = [];
        for ($i = 0; $i < 5; $i++) {
            $variants[] = ProductVariant::create([
                'product_id' => $product->id,
                'unit_id' => $unit->id,
                'unit_value' => 1,
                'sku' => 'SKU-' . $i,
                'quantity' => 100,
                'selling_price' => 100,
                'limit_price' => 50,
                'alert_quantity' => 10,
            ]);
        }

        $order = Order::forceCreate([
            'order_date' => now(),
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->mobile,
            'customer_address' => $customer->address,
            'status' => 'Pending',
            'order_number' => 'ORD-123',
        ]);

        // Add 10 items (repeating variants)
        foreach ($variants as $index => $variant) {
            // Add twice to simulate multiple items for same variant
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
            'product_name' => 'Test Product',
            'sku' => 'SKU-0',
                'quantity' => 2,
                'unit_price' => 100,
                'total_price' => 200,
            ]);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
            'product_name' => 'Test Product',
            'sku' => 'SKU-0',
                'quantity' => 3,
                'unit_price' => 100,
                'total_price' => 300,
            ]);
        }
        // Total 10 items. 5 unique variants.

        // Prepare payload for update (can be same items or new, we just want to trigger the revert logic)
        // We will just keep one item in the new order to make payload simple
        $payload = [
            'order_type' => 'direct',
            'order_date' => now()->toDateString(),
            'customer' => [
                'name' => 'John Doe Updated',
                'mobile' => '0712345678',
                'address' => '123 Main St',
            ],
            'items' => [
                [
                    'id' => $variants[0]->id,
                    'quantity' => 5,
                    'selling_price' => 100,
                ]
            ],
            'courier_id' => $courier->id,
        ];

        // 2. Measure Queries
        DB::enableQueryLog();

        $response = $this->putJson(route('orders.update', $order->id), $payload);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Debug output
        // foreach ($queries as $q) {
        //     echo "\n" . $q['query'];
        // }

        $response->assertStatus(200);

        echo "\nTotal Queries: " . $queryCount . "\n";
    }
}
