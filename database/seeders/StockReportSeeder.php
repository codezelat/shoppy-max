<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockReportSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks for speed
        DB::statement('PRAGMA foreign_keys = OFF');
        PurchaseItem::truncate();
        Purchase::truncate();
        Product::truncate();
        Supplier::truncate();
        DB::statement('PRAGMA foreign_keys = ON');

        $products = [];
        for ($i = 0; $i < 1000; $i++) {
            $products[] = [
                'name' => 'Product '.$i,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        foreach (array_chunk($products, 100) as $chunk) {
            Product::insert($chunk);
        }

        $productIds = Product::pluck('id')->toArray();

        // Create a Supplier
        $supplier = Supplier::create([
            'business_name' => 'Test Business',
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'mobile' => '1234567890',
            'address' => '123 Test St',
        ]);

        // Create 500 purchases
        $purchases = [];
        for ($i = 0; $i < 500; $i++) {
            $purchases[] = [
                'purchase_number' => 'PUR-'.$i,
                'supplier_id' => $supplier->id,
                'purchase_date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
                'sub_total' => 0,
                'net_total' => 0,
                'paid_amount' => 0,
            ];
        }
        foreach (array_chunk($purchases, 100) as $chunk) {
            Purchase::insert($chunk);
        }
        $purchaseIds = Purchase::pluck('id')->toArray();

        // Create 5000 purchase items
        $purchaseItems = [];
        foreach ($purchaseIds as $purchaseId) {
            // Each purchase has 10 items
            for ($j = 0; $j < 10; $j++) {
                $qty = rand(1, 100);
                $purchaseItems[] = [
                    'purchase_id' => $purchaseId,
                    'product_id' => $productIds[array_rand($productIds)],
                    'product_name' => 'Product Name',
                    'quantity' => $qty,
                    'remaining_quantity' => rand(0, $qty), // Some sold
                    'purchase_price' => rand(10, 1000),
                    'total' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert in chunks to avoid limits
        foreach (array_chunk($purchaseItems, 1000) as $chunk) {
            PurchaseItem::insert($chunk);
        }
    }
}
