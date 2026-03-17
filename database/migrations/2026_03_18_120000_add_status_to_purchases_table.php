<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'status')) {
                $table->string('status')->default('pending')->after('purchase_date');
            }
        });

        DB::table('purchases')
            ->whereNull('status')
            ->update(['status' => 'pending']);
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
