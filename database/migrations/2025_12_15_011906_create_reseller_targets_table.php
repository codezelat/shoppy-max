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
            $table->unsignedBigInteger('user_id');
            $table->string('target_type');
            $table->decimal('target_completed_price', 10, 2)->default(0);
            $table->decimal('target_not_completed_price', 10, 2)->default(0);
            $table->decimal('return_order_target_price', 10, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('ref_id')->nullable();
            $table->integer('target_pieces_qty')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
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
