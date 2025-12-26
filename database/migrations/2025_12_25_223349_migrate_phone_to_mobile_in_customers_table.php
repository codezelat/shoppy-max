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
        // 1. Copy phone to mobile where mobile is empty
        \Illuminate\Support\Facades\DB::table('customers')
            ->whereNull('mobile')
            ->orWhere('mobile', '')
            ->update(['mobile' => \Illuminate\Support\Facades\DB::raw('phone')]);

        // 2. Drop phone column
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('mobile');
        });

        // Restore phone from mobile
        \Illuminate\Support\Facades\DB::table('customers')
            ->update(['phone' => \Illuminate\Support\Facades\DB::raw('mobile')]);
    }
};
