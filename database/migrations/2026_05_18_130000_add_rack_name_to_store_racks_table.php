<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_racks', function (Blueprint $table) {
            if (! Schema::hasColumn('store_racks', 'rack_name')) {
                $table->string('rack_name', 100)->nullable()->after('store_type');
            }

            if (! Schema::hasColumn('store_racks', 'rack_key')) {
                $table->string('rack_key', 120)->nullable()->after('rack_name');
            }
        });

        DB::table('store_racks')
            ->whereNull('rack_name')
            ->update([
                'rack_name' => 'Default Rack',
                'rack_key' => 'default rack',
            ]);

        try {
            Schema::table('store_racks', function (Blueprint $table) {
                $table->dropUnique('store_racks_store_type_row_key_unique');
            });
        } catch (Throwable $e) {
            // Some existing SQLite/MySQL installs may not expose the old auto-named index.
        }

        Schema::table('store_racks', function (Blueprint $table) {
            $table->unique(['store_type', 'rack_key', 'row_key'], 'store_racks_store_rack_row_unique');
        });
    }

    public function down(): void
    {
        try {
            Schema::table('store_racks', function (Blueprint $table) {
                $table->dropUnique('store_racks_store_rack_row_unique');
            });
        } catch (Throwable $e) {
        }

        Schema::table('store_racks', function (Blueprint $table) {
            foreach (['rack_key', 'rack_name'] as $column) {
                if (Schema::hasColumn('store_racks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        try {
            Schema::table('store_racks', function (Blueprint $table) {
                $table->unique(['store_type', 'row_key']);
            });
        } catch (Throwable $e) {
        }
    }
};
