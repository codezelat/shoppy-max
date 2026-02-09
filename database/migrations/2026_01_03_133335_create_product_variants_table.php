<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->string('sku')->unique();
            $table->decimal('selling_price', 10, 2);
            $table->decimal('limit_price', 10, 2)->nullable();
            $table->integer('alert_quantity')->default(0);
            $table->integer('quantity')->default(0); // Current stock
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // Migrate existing data
        $products = DB::table('products')->get();
        $hasUnitId = Schema::hasColumn('products', 'unit_id');

        foreach ($products as $product) {
            DB::table('product_variants')->insert([
                'product_id' => $product->id,
                'unit_id' => $hasUnitId ? $product->unit_id : null,
                'sku' => $product->sku,
                'selling_price' => $product->selling_price,
                'limit_price' => $product->limit_price,
                'alert_quantity' => $product->alert_quantity,
                'quantity' => $product->quantity,
                'image' => null,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ]);
        }

        // Remove columns from products table safely
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'unit_id')) {
                try {
                    $table->dropForeign(['unit_id']);
                } catch (\Exception $e) {
                }
            }
        });

        // Safely drop index if exists (SQLite specific safe handling, mostly)
        try {
            DB::statement('DROP INDEX IF EXISTS products_sku_unique');
        } catch (\Exception $e) {
            // Check if it's MySQL and try standard syntax if needed, or just ignore
        }

        Schema::table('products', function (Blueprint $table) {
            $columnsToDrop = [];
            $candidates = ['unit_id', 'sku', 'selling_price', 'limit_price', 'alert_quantity', 'quantity'];
            foreach ($candidates as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->string('sku')->nullable(); // Nullable initially to allow adding back
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->decimal('limit_price', 10, 2)->nullable();
            $table->integer('alert_quantity')->default(0);
            $table->integer('quantity')->default(0);
        });

        // Restore data (Best effort: Take the first variant)
        $variants = DB::table('product_variants')->get()->groupBy('product_id');
        foreach ($variants as $productId => $productVariants) {
            $firstVariant = $productVariants->first();
            DB::table('products')->where('id', $productId)->update([
                'unit_id' => $firstVariant->unit_id,
                'sku' => $firstVariant->sku,
                'selling_price' => $firstVariant->selling_price,
                'limit_price' => $firstVariant->limit_price,
                'alert_quantity' => $firstVariant->alert_quantity,
                'quantity' => $firstVariant->quantity,
            ]);
        }

        // Restore unique constraint on SKU after populating
        Schema::table('products', function (Blueprint $table) {
            $table->unique('sku');
        });

        Schema::dropIfExists('product_variants');
    }
};
