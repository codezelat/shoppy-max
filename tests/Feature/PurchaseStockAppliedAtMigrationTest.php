<?php

namespace Tests\Feature;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseStockAppliedAtMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_incomplete_purchase_stock_applied_timestamps_are_cleared_by_corrective_migration(): void
    {
        $supplier = Supplier::create([
            'business_name' => 'Legacy Supplier',
            'name' => 'Legacy Supplier',
            'mobile' => '0711111111',
        ]);

        $pendingPurchase = $this->makePurchase($supplier, 'LEGACY-PENDING', 'pending', now());
        $checkingPurchase = $this->makePurchase($supplier, 'LEGACY-CHECKING', 'checking', now());
        $verifiedPurchase = $this->makePurchase($supplier, 'LEGACY-VERIFIED', 'verified', now());
        $completePurchase = $this->makePurchase($supplier, 'LEGACY-COMPLETE', 'complete', now());

        $migration = require base_path('database/migrations/2026_05_18_000000_clear_stock_applied_at_from_incomplete_purchases.php');
        $migration->up();

        $this->assertNull($pendingPurchase->fresh()->stock_applied_at);
        $this->assertNull($checkingPurchase->fresh()->stock_applied_at);
        $this->assertNull($verifiedPurchase->fresh()->stock_applied_at);
        $this->assertNotNull($completePurchase->fresh()->stock_applied_at);
    }

    private function makePurchase(Supplier $supplier, string $purchaseNumber, string $status, $stockAppliedAt): Purchase
    {
        return Purchase::create([
            'purchase_number' => $purchaseNumber,
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'status' => $status,
            'completed_at' => $status === 'complete' ? now() : null,
            'stock_applied_at' => $stockAppliedAt,
            'currency' => 'LKR',
            'sub_total' => 0,
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'discount_amount' => 0,
            'net_total' => 0,
            'paid_amount' => 0,
        ]);
    }
}
