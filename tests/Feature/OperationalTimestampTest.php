<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Purchase;
use Carbon\Carbon;
use Database\Seeders\DemoSystemSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationalTimestampTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_app_uses_business_timezone_by_default(): void
    {
        $this->assertSame('Asia/Colombo', config('app.timezone'));
    }

    public function test_demo_purchase_audit_times_match_purchase_dates(): void
    {
        Carbon::setTestNow('2026-05-18 02:00:00');
        $this->seed([RolesAndPermissionsSeeder::class, DemoSystemSeeder::class]);

        $purchases = Purchase::where('purchase_number', 'like', 'PUR-DEMO-%')->get();

        $this->assertNotEmpty($purchases);

        foreach ($purchases as $purchase) {
            $purchaseDate = $purchase->purchase_date->format('Y-m-d');

            $this->assertSame($purchaseDate, $purchase->created_at->format('Y-m-d'));
            $this->assertSame('Asia/Colombo', $purchase->created_at->timezoneName);
            $this->assertLessThanOrEqual(now(), $purchase->created_at);

            if ($purchase->checked_at) {
                $this->assertGreaterThanOrEqual($purchase->created_at, $purchase->checked_at);
                $this->assertLessThanOrEqual(now(), $purchase->checked_at);
            }

            if ($purchase->verified_at) {
                $this->assertGreaterThanOrEqual($purchase->checked_at ?? $purchase->created_at, $purchase->verified_at);
                $this->assertLessThanOrEqual(now(), $purchase->verified_at);
            }

            if ($purchase->completed_at) {
                $this->assertGreaterThanOrEqual($purchase->verified_at ?? $purchase->created_at, $purchase->completed_at);
                $this->assertLessThanOrEqual(now(), $purchase->completed_at);
            }
        }
    }

    public function test_demo_order_timeline_times_match_order_dates(): void
    {
        Carbon::setTestNow('2026-05-18 02:00:00');
        $this->seed([RolesAndPermissionsSeeder::class, DemoSystemSeeder::class]);

        $orders = Order::where('order_number', 'like', 'DEMO-ORD-%')->get();

        $this->assertNotEmpty($orders);

        foreach ($orders as $order) {
            $orderDate = $order->order_date->format('Y-m-d');

            $this->assertSame($orderDate, $order->created_at->format('Y-m-d'));
            $this->assertSame('Asia/Colombo', $order->created_at->timezoneName);
            $this->assertLessThanOrEqual(now(), $order->created_at);

            foreach ([
                'waybill_printed_at',
                'waybill_excel_exported_at',
                'picked_at',
                'packed_at',
                'dispatched_at',
                'cancelled_at',
                'delivered_at',
                'returned_at',
            ] as $column) {
                if (! $order->{$column}) {
                    continue;
                }

                $this->assertSame($orderDate, $order->{$column}->format('Y-m-d'), "{$column} should stay on order date.");
                $this->assertGreaterThanOrEqual($order->created_at, $order->{$column});
                $this->assertLessThanOrEqual(now(), $order->{$column});
            }
        }
    }
}
