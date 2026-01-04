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
        Schema::create('reseller_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained('resellers')->onDelete('cascade');
            $table->string('target_type'); // daily, weekly, monthly
            $table->decimal('target_completed_price', 15, 2)->nullable();
            $table->decimal('with_completed_price', 15, 2)->nullable();
            $table->decimal('return_order_target_price', 15, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('ref_id')->nullable();
            $table->integer('target_pcs_qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_targets');
    }
};
