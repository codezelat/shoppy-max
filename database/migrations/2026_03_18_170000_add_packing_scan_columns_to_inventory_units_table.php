<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_units', function (Blueprint $table) {
            $table->timestamp('packed_scan_at')->nullable()->after('allocated_at');
            $table->foreignId('packed_scan_user_id')->nullable()->after('packed_scan_at')->constrained('users')->nullOnDelete();

            $table->index(['order_id', 'packed_scan_at']);
        });
    }

    public function down(): void
    {
        Schema::table('inventory_units', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'packed_scan_at']);
            $table->dropConstrainedForeignId('packed_scan_user_id');
            $table->dropColumn('packed_scan_at');
        });
    }
};
