<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (!Schema::hasColumn('orders', 'waybill_excel_exported_at')) {
                $table->timestamp('waybill_excel_exported_at')->nullable()->after('waybill_printed_by');
            }

            if (!Schema::hasColumn('orders', 'waybill_excel_exported_by')) {
                $table->foreignId('waybill_excel_exported_by')
                    ->nullable()
                    ->after('waybill_excel_exported_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $drops = [];

            if (Schema::hasColumn('orders', 'waybill_excel_exported_by')) {
                $table->dropConstrainedForeignId('waybill_excel_exported_by');
            }

            if (Schema::hasColumn('orders', 'waybill_excel_exported_at')) {
                $drops[] = 'waybill_excel_exported_at';
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
