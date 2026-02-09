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
            // Drop existing foreign key (assuming standard naming convention)
            $table->dropForeign(['reseller_id']);

            // Add new foreign key referencing resellers table
            $table->foreign('reseller_id')
                ->references('id')
                ->on('resellers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['reseller_id']);

            // Revert to referencing users table
            $table->foreign('reseller_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }
};
