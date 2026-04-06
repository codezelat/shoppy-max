<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('purchase_items') || ! Schema::hasColumn('purchase_items', 'product_id')) {
            return;
        }

        if (! $this->foreignKeyExists('purchase_items', 'product_id')) {
            return;
        }

        Schema::table('purchase_items', function (Blueprint $table) {
            // Drop the foreign key constraint on product_id
            // This allows us to store product_id even if the product doesn't exist
            // We keep product_name as the source of truth
            $table->dropForeign(['product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('purchase_items') || ! Schema::hasColumn('purchase_items', 'product_id')) {
            return;
        }

        if ($this->foreignKeyExists('purchase_items', 'product_id', 'products')) {
            return;
        }

        Schema::table('purchase_items', function (Blueprint $table) {
            // Re-add the foreign key constraint
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    private function foreignKeyExists(string $table, string $column, ?string $referencedTable = null): bool
    {
        return match (DB::getDriverName()) {
            'mysql', 'mariadb' => DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', $table)
                ->where('COLUMN_NAME', $column)
                ->when($referencedTable !== null, fn ($query) => $query->where('REFERENCED_TABLE_NAME', $referencedTable))
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->exists(),
            'sqlite' => collect(DB::select("PRAGMA foreign_key_list('{$table}')"))
                ->contains(function ($foreignKey) use ($column, $referencedTable) {
                    return $foreignKey->from === $column
                        && ($referencedTable === null || $foreignKey->table === $referencedTable);
                }),
            default => false,
        };
    }
};
