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
            if (!Schema::hasColumn('orders', 'courier_id')) {
                $table->foreignId('courier_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('orders', 'courier_charge')) {
                $table->decimal('courier_charge', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (!Schema::hasColumn('orders', 'call_status')) {
                $table->string('call_status')->nullable();
            }
            if (!Schema::hasColumn('orders', 'sales_note')) {
                $table->text('sales_note')->nullable();
            }
            if (!Schema::hasColumn('orders', 'customer_city')) {
                $table->string('customer_city')->nullable();
            }
            if (!Schema::hasColumn('orders', 'customer_district')) {
                $table->string('customer_district')->nullable();
            }
            if (!Schema::hasColumn('orders', 'customer_province')) {
                $table->string('customer_province')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
