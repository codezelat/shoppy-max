<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('store_racks')) {
            Schema::create('store_racks', function (Blueprint $table) {
                $table->id();
                $table->string('store_type', 32);
                $table->string('row_name', 100);
                $table->string('row_key', 120);
                $table->timestamps();

                $table->unique(['store_type', 'row_key']);
                $table->index('store_type');
            });
        }

        Schema::table('inventory_units', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_units', 'store_type')) {
                $table->string('store_type', 32)->nullable()->after('status')->index();
            }

            if (! Schema::hasColumn('inventory_units', 'store_rack_id')) {
                $table->foreignId('store_rack_id')
                    ->nullable()
                    ->after('store_type')
                    ->constrained('store_racks')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('inventory_units', 'stored_at')) {
                $table->timestamp('stored_at')->nullable()->after('available_at');
            }

            if (! Schema::hasColumn('inventory_units', 'stored_by')) {
                $table->foreignId('stored_by')
                    ->nullable()
                    ->after('stored_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_units', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_units', 'stored_by')) {
                $table->dropConstrainedForeignId('stored_by');
            }

            if (Schema::hasColumn('inventory_units', 'store_rack_id')) {
                $table->dropConstrainedForeignId('store_rack_id');
            }

            foreach (['stored_at', 'store_type'] as $column) {
                if (Schema::hasColumn('inventory_units', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('store_racks');
    }
};
