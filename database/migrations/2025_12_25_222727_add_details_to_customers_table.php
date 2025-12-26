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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('name');
            $table->string('email')->nullable()->after('business_name');
            $table->string('mobile')->nullable()->after('email'); // Assuming 'phone' exists, we add 'mobile' separately or use phone.
            // If phone is intended to be mobile, we might not need this, but user asked for "mobile".
            // To be safe and explicit, I will add mobile.
            $table->string('landline')->nullable()->after('mobile');
            $table->string('country')->default('Sri Lanka')->after('address');
            $table->string('province')->nullable()->after('country');
            $table->string('district')->nullable()->after('province');
            $table->string('city')->nullable()->after('district');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'email',
                'mobile',
                'landline',
                'country',
                'province',
                'district',
                'city',
            ]);
        });
    }
};
