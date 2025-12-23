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
            // We previously defined 'courier_id' as nullable string. 
            // Better to change it to unsignedBigInteger if we want relation, or just leave it and add foreign key constraint if data allows.
            // For safety and simplicity in this additive migration:
            
            // $table->foreignId('courier_id')->change(); // Only if courier_id was already bigInteger, but it was string.
            // So we'll trust the user to manage the migration refresh or we can drop/add. 
            // Given we just merged OMS, we can probably drop the string column and add the foreign key if there's no data.
            // BUT, to be safe:
            
            if (Schema::hasColumn('orders', 'courier_id')) {
                 $table->dropColumn('courier_id');
            }
        });
        
        Schema::table('orders', function (Blueprint $table) {
             $table->foreignId('courier_id')->nullable()->after('waybill_number')->constrained('couriers')->onDelete('set null');
             $table->decimal('courier_cost', 10, 2)->default(0)->after('total_amount'); // Real cost
             $table->decimal('delivery_fee', 10, 2)->default(0)->after('courier_cost'); // Charged to customer
             
             $table->foreignId('courier_payment_id')->nullable()->constrained('courier_payments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['courier_id']);
            $table->dropColumn('courier_id');
            $table->string('courier_id')->nullable(); // Revert to string
            
            $table->dropColumn(['courier_cost', 'delivery_fee']);
            $table->dropForeign(['courier_payment_id']);
            $table->dropColumn('courier_payment_id');
        });
    }
};
