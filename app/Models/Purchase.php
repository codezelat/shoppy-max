<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public const STATUSES = [
        'pending',
        'checking',
        'verified',
        'complete',
    ];

    protected $fillable = [
        'purchase_number',
        'supplier_id',
        'purchase_date',
        'status',
        'currency',
        'sub_total',
        'discount_type',
        'discount_value',
        'discount_amount',
        'net_total',
        'paid_amount',
        'payments_data',
        'payment_method',
        'payment_reference',
        'payment_account',
        'payment_note',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'payments_data' => 'array',
        'sub_total' => 'decimal:2',
        'net_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function getPaymentStatusAttribute(): string
    {
        $netTotal = (float) ($this->net_total ?? 0);
        $paidAmount = (float) ($this->paid_amount ?? 0);

        if ($netTotal <= 0 || $paidAmount >= $netTotal) {
            return 'paid';
        }

        if ($paidAmount > 0) {
            return 'partial';
        }

        return 'due';
    }
}
