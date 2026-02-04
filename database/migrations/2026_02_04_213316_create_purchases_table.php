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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_number')->unique();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('purchase_date');
            $table->string('currency')->default('LKR');
            
            // Financials
            $table->decimal('sub_total', 12, 2)->default(0);
            $table->string('discount_type')->default('fixed'); // fixed or percentage
            $table->decimal('discount_value', 12, 2)->default(0); // The entered value (e.g., 10 for 10%)
            $table->decimal('discount_amount', 12, 2)->default(0); // The calculated amount to subtract
            $table->decimal('net_total', 12, 2)->default(0);
            
            // Payments
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('payment_method')->nullable(); // Cash, Card, Cheque, Other
            $table->string('payment_reference')->nullable();
            $table->string('payment_account')->nullable();
            $table->text('payment_note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
