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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Creator (Admin or Reseller)
            $table->foreignId('reseller_id')->nullable()->constrained('users')->onDelete('set null'); // If linked to a reseller
            
            // Customer Details (Denormalized for snapshot or linked if Customer model exists, sticking to requirements for now)
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null');

            // Order Status
            $table->string('status')->default('pending'); // pending, on_hold, confirmed, packing, dispatched, delivered, returned, cancelled
            
            // Payment
            $table->string('payment_method')->default('cod'); // cod, online
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            
            // Financials
            $table->decimal('total_amount', 10, 2)->default(0);
            
            // Meta
            $table->text('sales_note')->nullable();
            
            // Logistics
            $table->string('waybill_number')->nullable();
            $table->string('courier_id')->nullable(); // Using string for now or foreign key if courier table exists
            
            // Workflow Tracking
            $table->foreignId('packed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('returned_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
