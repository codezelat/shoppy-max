<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('orders')
            ->where('call_status', 'cancel')
            ->update(['call_status' => 'hold']);

        DB::table('orders')
            ->whereNull('call_status')
            ->orWhereNotIn('call_status', ['pending', 'confirm', 'hold'])
            ->update(['call_status' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a one-way data normalization migration.
    }
};
