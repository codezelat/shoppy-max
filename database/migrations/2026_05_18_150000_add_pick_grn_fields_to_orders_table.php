<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'pick_grn_number')) {
                $table->string('pick_grn_number')->nullable()->unique()->after('waybill_number');
            }

            if (! Schema::hasColumn('orders', 'pick_grn_created_at')) {
                $table->timestamp('pick_grn_created_at')->nullable()->after('pick_grn_number');
            }

            if (! Schema::hasColumn('orders', 'pick_grn_created_by')) {
                $table->foreignId('pick_grn_created_by')
                    ->nullable()
                    ->after('pick_grn_created_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'pick_grn_created_by')) {
                $table->dropConstrainedForeignId('pick_grn_created_by');
            }

            foreach (['pick_grn_created_at', 'pick_grn_number'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
