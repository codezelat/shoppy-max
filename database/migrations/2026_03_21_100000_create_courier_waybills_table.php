<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_waybills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('prefix')->nullable();
            $table->unsignedBigInteger('sequence_number');
            $table->string('suffix')->nullable();
            $table->unsignedBigInteger('range_start');
            $table->unsignedBigInteger('range_end');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('allocated_at')->nullable();
            $table->timestamps();

            $table->index(['courier_id', 'order_id']);
            $table->index(['courier_id', 'allocated_at']);
        });

        if (!Schema::hasTable('orders')) {
            return;
        }

        $existingWaybills = DB::table('orders')
            ->select('id', 'courier_id', 'waybill_number', 'created_at', 'updated_at')
            ->whereNotNull('courier_id')
            ->whereNotNull('waybill_number')
            ->where('waybill_number', '!=', '')
            ->orderBy('id')
            ->get();

        if ($existingWaybills->isEmpty()) {
            return;
        }

        $timestamp = now();

        $rows = $existingWaybills->map(function ($order) use ($timestamp) {
            return [
                'courier_id' => $order->courier_id,
                'code' => (string) $order->waybill_number,
                'prefix' => null,
                'sequence_number' => (int) $order->id,
                'suffix' => null,
                'range_start' => (int) $order->id,
                'range_end' => (int) $order->id,
                'order_id' => (int) $order->id,
                'allocated_at' => $order->created_at ?? $timestamp,
                'created_at' => $order->created_at ?? $timestamp,
                'updated_at' => $order->updated_at ?? $timestamp,
            ];
        })->all();

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('courier_waybills')->insertOrIgnore($chunk);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_waybills');
    }
};
