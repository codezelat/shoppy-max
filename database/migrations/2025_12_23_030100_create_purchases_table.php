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
            $table->string('purchasing_number')->unique();
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Creator
            
            $table->string('status')->default('pending'); // pending, verified, received, cancelled
            $table->decimal('total_amount', 10, 2)->default(0);
            
            $table->string('grn_number')->nullable()->unique(); // Generated on verification
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
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
