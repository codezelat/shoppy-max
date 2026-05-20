<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Courier;
use App\Models\InventoryUnit;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Reseller;
use App\Models\Unit;
use App\Services\ResellerAccountService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ResellerOwnOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_reseller_roles_receive_default_own_order_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $expected = [
            'view own orders',
            'create own orders',
            'edit own orders',
            'delete own orders',
            'cancel own orders',
            'export own orders',
            'print own orders',
        ];

        $resellerRole = Role::where('name', 'reseller')->firstOrFail();
        $directResellerRole = Role::where('name', 'direct reseller')->firstOrFail();

        foreach ($expected as $permission) {
            $this->assertTrue($resellerRole->hasPermissionTo($permission), "Reseller role is missing {$permission}.");
            $this->assertTrue($directResellerRole->hasPermissionTo($permission), "Direct reseller role is missing {$permission}.");
        }
    }

    public function test_reseller_order_list_is_scoped_to_linked_account(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $reseller = $this->makeReseller('Own Order Reseller', 'own.orders@example.test');
        $otherReseller = $this->makeReseller('Other Order Reseller', 'other.orders@example.test');
        $this->syncAccount($reseller);
        $this->syncAccount($otherReseller);

        $ownOrder = $this->makeOrder('ORD-20260520-1001', $reseller, 'Own Customer');
        $otherOrder = $this->makeOrder('ORD-20260520-1002', $otherReseller, 'Other Customer');
        $directOrder = Order::forceCreate([
            'order_number' => 'ORD-20260520-1003',
            'order_date' => now()->toDateString(),
            'order_type' => 'direct',
            'status' => 'pending',
            'call_status' => 'pending',
            'delivery_status' => 'pending',
            'payment_method' => 'COD',
            'payment_status' => 'pending',
            'total_amount' => 500,
        ]);

        $response = $this->actingAs($reseller->userAccount)->get(route('orders.index'));

        $response->assertOk();
        $response->assertSee($ownOrder->order_number);
        $response->assertSee('Own Customer');
        $response->assertDontSee($otherOrder->order_number);
        $response->assertDontSee($directOrder->order_number);
        $response->assertDontSee('Other Customer');
    }

    public function test_regular_reseller_create_order_forces_linked_owner_and_reseller_pricing(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        [$city, $variant, $courier] = $this->orderDependencies();
        $reseller = $this->makeReseller('Regular Portal Reseller', 'regular.portal@example.test');
        $otherReseller = $this->makeReseller('Forged Reseller', 'forged.reseller@example.test');
        $this->syncAccount($reseller);

        $response = $this->actingAs($reseller->userAccount)->postJson(route('orders.store'), $this->orderPayload($city, $variant, $courier, [
            'order_type' => 'direct',
            'reseller_id' => $otherReseller->id,
            'items' => [
                [
                    'id' => $variant->id,
                    'quantity' => 1,
                    'selling_price' => 180,
                ],
            ],
        ]));

        $response->assertOk()->assertJson(['success' => true]);

        $order = Order::with('items')->latest('id')->firstOrFail();
        $item = $order->items->first();

        $this->assertSame('reseller', $order->order_type);
        $this->assertSame($reseller->id, $order->reseller_id);
        $this->assertSame($reseller->user_id, $order->user_id);
        $this->assertSame('180.00', $item->unit_price);
        $this->assertSame('80.00', $order->total_commission);
    }

    public function test_direct_reseller_create_order_forces_linked_owner_without_regular_reseller_commission(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        [$city, $variant, $courier] = $this->orderDependencies();
        $directReseller = $this->makeReseller(
            'Direct Portal Reseller',
            'direct.portal@example.test',
            Reseller::TYPE_DIRECT_RESELLER
        );
        $otherReseller = $this->makeReseller('Forged Direct Owner', 'forged.direct@example.test');
        $this->syncAccount($directReseller);

        $response = $this->actingAs($directReseller->userAccount)->postJson(route('orders.store'), $this->orderPayload($city, $variant, $courier, [
            'order_type' => 'direct',
            'reseller_id' => $otherReseller->id,
            'items' => [
                [
                    'id' => $variant->id,
                    'quantity' => 1,
                    'selling_price' => 180,
                ],
            ],
        ]));

        $response->assertOk()->assertJson(['success' => true]);

        $order = Order::with('items')->latest('id')->firstOrFail();
        $item = $order->items->first();

        $this->assertSame('reseller', $order->order_type);
        $this->assertSame($directReseller->id, $order->reseller_id);
        $this->assertSame('180.00', $item->unit_price);
        $this->assertSame('0.00', $order->total_commission);
    }

    public function test_reseller_user_can_only_view_update_cancel_and_delete_own_orders(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $reseller = $this->makeReseller('Scoped Reseller', 'scoped@example.test');
        $otherReseller = $this->makeReseller('Blocked Reseller', 'blocked@example.test');
        $this->syncAccount($reseller);
        $this->syncAccount($otherReseller);

        $ownOrder = $this->makeOrder('ORD-20260520-1010', $reseller, 'Scoped Customer');
        $otherOrder = $this->makeOrder('ORD-20260520-1011', $otherReseller, 'Blocked Customer');

        $this->actingAs($reseller->userAccount)->get(route('orders.show', $ownOrder))->assertOk();
        $this->actingAs($reseller->userAccount)->get(route('orders.show', $otherOrder))->assertForbidden();
        $this->actingAs($reseller->userAccount)->get(route('orders.edit', $otherOrder))->assertForbidden();
        $this->actingAs($reseller->userAccount)->delete(route('orders.destroy', $otherOrder))->assertForbidden();

        $this->actingAs($reseller->userAccount)
            ->postJson(route('orders.status.update', $ownOrder), ['call_status' => 'hold'])
            ->assertForbidden();

        $this->actingAs($reseller->userAccount)
            ->postJson(route('orders.status.update', $ownOrder), ['status' => 'cancel'])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'cancel',
                'call_status' => 'cancel',
                'delivery_status' => 'cancel',
            ]);

        $deleteOrder = $this->makeOrder('ORD-20260520-1012', $reseller, 'Delete Customer');
        $this->actingAs($reseller->userAccount)
            ->delete(route('orders.destroy', $deleteOrder))
            ->assertRedirect(route('orders.index'));

        $this->assertSoftDeleted('orders', ['id' => $deleteOrder->id]);
        $this->assertSame('pending', $otherOrder->fresh()->status);
    }

    private function makeReseller(
        string $name,
        string $email,
        string $type = Reseller::TYPE_RESELLER
    ): Reseller {
        return Reseller::create([
            'business_name' => $name,
            'name' => $name.' Contact',
            'email' => $email,
            'mobile' => '077'.str_pad((string) Reseller::count(), 7, '0', STR_PAD_LEFT),
            'address' => '123 Portal Road',
            'country' => 'Sri Lanka',
            'province' => 'Western',
            'district' => 'Colombo',
            'city' => 'Colombo',
            'due_amount' => 0,
            'return_fee' => 50,
            'reseller_type' => $type,
        ]);
    }

    private function syncAccount(Reseller $reseller): void
    {
        app(ResellerAccountService::class)->syncSeedAccount($reseller, 'password');
        $reseller->refresh()->load('userAccount');
    }

    private function makeOrder(string $number, Reseller $reseller, string $customerName): Order
    {
        return Order::forceCreate([
            'order_number' => $number,
            'order_date' => now()->toDateString(),
            'order_type' => 'reseller',
            'reseller_id' => $reseller->id,
            'user_id' => $reseller->user_id,
            'customer_name' => $customerName,
            'customer_phone' => '0771234567',
            'status' => 'pending',
            'call_status' => 'pending',
            'delivery_status' => 'pending',
            'payment_method' => 'COD',
            'payment_status' => 'pending',
            'total_amount' => 500,
        ]);
    }

    private function orderPayload(City $city, ProductVariant $variant, Courier $courier, array $overrides = []): array
    {
        return array_replace_recursive([
            'order_type' => 'direct',
            'customer' => [
                'name' => 'Portal Order Customer',
                'mobile' => '0771234567',
                'landline' => '',
                'address' => '123 Test Road',
                'city_id' => $city->id,
                'district' => $city->district,
                'province' => $city->province,
            ],
            'items' => [
                [
                    'id' => $variant->id,
                    'quantity' => 1,
                    'selling_price' => $variant->selling_price,
                ],
            ],
            'courier_id' => $courier->id,
            'courier_charge' => 0,
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'payment_method' => 'COD',
            'payment_status' => 'pending',
            'sales_note' => '',
        ], $overrides);
    }

    private function orderDependencies(): array
    {
        $city = City::create([
            'city_name' => 'Colombo 01',
            'postal_code' => '00100',
            'district' => 'Colombo',
            'province' => 'Western',
        ]);
        $courier = Courier::create([
            'name' => 'Portal Courier',
            'rates' => [0],
            'is_active' => true,
        ]);
        $category = Category::create(['name' => 'Portal Category', 'code' => 'PORTAL']);
        $unit = Unit::create(['name' => 'Piece', 'short_name' => 'pcs']);
        $product = Product::create([
            'name' => 'Portal Product',
            'category_id' => $category->id,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'unit_id' => $unit->id,
            'sku' => 'SKU-PORTAL-ORDER',
            'selling_price' => 250,
            'limit_price' => 100,
            'quantity' => 4,
            'alert_quantity' => 0,
        ]);

        foreach (range(1, 4) as $index) {
            InventoryUnit::create([
                'product_variant_id' => $variant->id,
                'unit_code' => 'UNIT-PORTAL-ORDER-'.$index,
                'status' => InventoryUnit::STATUS_AVAILABLE,
                'sku_snapshot' => $variant->sku,
                'product_name_snapshot' => $product->name,
                'variant_label_snapshot' => 'Piece',
                'available_at' => now(),
                'last_event_at' => now(),
            ]);
        }

        return [$city, $variant, $courier];
    }
}
