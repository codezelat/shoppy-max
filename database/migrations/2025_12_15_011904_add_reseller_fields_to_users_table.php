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
        Schema::table('users', function (Blueprint $table) {
            $table->string('branch')->nullable();
            $table->decimal('return_fee', 10, 2)->nullable();
            $table->unsignedBigInteger('courier_id')->nullable(); 
            // We assume courier_id links to a couriers table. If not created yet, we just store the ID for now or make it a string if the user prefers. 
            // Given the requirement "Courier" in the form, let's keep it as nullable integer or string. Let's start with string if no courier table exists, but wait, usually Courier is an entity. 
            // Let's assume nullable string for simplicity if "Courier" is just a name, or ID if it's a relation. 
            // The prompt said "Courier" dropdown. Let's make it unsignedBigInteger but NOT foreign key constrained yet to avoid errors if table doesn't exist, or just nullable string if typically just a name.
            // Let's use string for 'courier_name' or similar to be safe, OR unsignedBigInteger. Let's go with unsignedBigInteger but no constraint.
            
            $table->unsignedBigInteger('parent_id')->nullable(); // For sub-users
            $table->string('user_type')->default('user'); // admin, reseller, direct_reseller, sub_user
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['branch', 'return_fee', 'courier_id', 'parent_id', 'user_type']);
        });
    }
};
