<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            ! Schema::hasTable('purchases')
            || ! Schema::hasColumn('purchases', 'status')
            || ! Schema::hasColumn('purchases', 'stock_applied_at')
        ) {
            return;
        }

        DB::table('purchases')
            ->whereNotNull('stock_applied_at')
            ->where(function ($query) {
                $query
                    ->where('status', '!=', 'complete')
                    ->orWhereNull('status');
            })
            ->update(['stock_applied_at' => null]);
    }

    public function down(): void
    {
        //
    }
};
