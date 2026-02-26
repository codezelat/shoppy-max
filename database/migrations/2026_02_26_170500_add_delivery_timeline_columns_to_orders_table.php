<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'waybill_printed_at')) {
                $table->timestamp('waybill_printed_at')->nullable()->after('delivery_status');
            }
            if (!Schema::hasColumn('orders', 'picked_at')) {
                $table->timestamp('picked_at')->nullable()->after('waybill_printed_at');
            }
            if (!Schema::hasColumn('orders', 'packed_at')) {
                $table->timestamp('packed_at')->nullable()->after('picked_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $drops = [];

            if (Schema::hasColumn('orders', 'packed_at')) {
                $drops[] = 'packed_at';
            }
            if (Schema::hasColumn('orders', 'picked_at')) {
                $drops[] = 'picked_at';
            }
            if (Schema::hasColumn('orders', 'waybill_printed_at')) {
                $drops[] = 'waybill_printed_at';
            }

            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};

