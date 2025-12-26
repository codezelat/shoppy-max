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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
            $table->string('landline')->nullable()->after('mobile');
            $table->text('address')->nullable()->after('landline');
            $table->string('city')->nullable()->after('address');
            $table->string('district')->nullable()->after('city');
            $table->string('province')->nullable()->after('district');
            $table->string('country')->nullable()->default('Sri Lanka')->after('province');
        });

        Schema::table('resellers', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
            $table->string('landline')->nullable()->after('mobile');
            $table->text('address')->nullable()->after('landline');
            $table->string('city')->nullable()->after('address');
            $table->string('district')->nullable()->after('city');
            $table->string('province')->nullable()->after('district');
            $table->string('country')->nullable()->default('Sri Lanka')->after('province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['email', 'landline', 'address', 'city', 'district', 'province', 'country']);
        });

        Schema::table('resellers', function (Blueprint $table) {
            $table->dropColumn(['email', 'landline', 'address', 'city', 'district', 'province', 'country']);
        });
    }
};
