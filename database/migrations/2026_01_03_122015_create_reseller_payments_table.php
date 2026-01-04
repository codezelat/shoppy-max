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
        Schema::create('reseller_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method'); // bank, cash, other
            $table->string('reference_id')->nullable();
            $table->timestamp('payment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_payments');
    }
};
