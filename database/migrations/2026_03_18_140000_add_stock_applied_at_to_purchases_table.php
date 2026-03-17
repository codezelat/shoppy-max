<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'stock_applied_at')) {
                $table->timestamp('stock_applied_at')->nullable()->after('completed_at');
            }
        });

        DB::table('purchases')
            ->whereNull('stock_applied_at')
            ->update([
                'stock_applied_at' => DB::raw('COALESCE(completed_at, updated_at, created_at)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'stock_applied_at')) {
                $table->dropColumn('stock_applied_at');
            }
        });
    }
};
