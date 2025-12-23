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
        Schema::create('seller_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('target_type');
            $table->decimal('target_completed_price', 10, 2)->nullable();
            $table->decimal('target_not_completed_price', 10, 2)->nullable();
            $table->decimal('return_order_target_price', 10, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('ref_id')->nullable();
            $table->integer('target_pieces_qty')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_targets');
    }
};
