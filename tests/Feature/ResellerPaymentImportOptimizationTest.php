<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Reseller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ResellerPaymentImportOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_store_performance()
    {
        // Setup
        $user = User::factory()->create();
        $this->actingAs($user);

        // Seed 50 Resellers
        $resellers = collect();
        for ($i = 0; $i < 50; $i++) {
            $resellers->push(Reseller::create([
                'business_name' => 'Reseller ' . $i,
                'name' => 'Owner ' . $i,
                'mobile' => '077123456' . $i,
                'due_amount' => 10000,
            ]));
        }

        // Create 1000 simulated payment records
        $previewData = [];
        $resellerIds = $resellers->pluck('id');
        for ($i = 0; $i < 1000; $i++) {
            $previewData[] = [
                'reseller_id' => $resellerIds->random(),
                'amount' => 100,
                'method' => 'cash',
                'reference' => 'REF' . $i,
                'date' => now()->format('Y-m-d'),
                'errors' => []
            ];
        }

        // Set session data
        session(['import_preview_data' => $previewData]);

        // Enable query logging
        DB::enableQueryLog();
        $startTime = microtime(true);

        // Call the store method via route
        $response = $this->post(route('reseller-payments.import.store'));

        $endTime = microtime(true);
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        $executionTime = $endTime - $startTime;

        // Output results
        echo "\n\nBenchmark Results:\n";
        echo "Execution Time: " . round($executionTime, 4) . " seconds\n";
        echo "Total Queries: " . $queryCount . "\n\n";

        // Assertions
        $response->assertRedirect(route('reseller-payments.index'));
        $this->assertDatabaseCount('reseller_payments', 1000);
    }
}
