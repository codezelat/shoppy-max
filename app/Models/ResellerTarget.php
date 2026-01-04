<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResellerTarget extends Model
{
    protected $fillable = [
        'reseller_id',
        'target_type',
        'target_completed_price',
        'with_completed_price',
        'return_order_target_price',
        'start_date',
        'end_date',
        'ref_id',
        'target_pcs_qty',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_completed_price' => 'decimal:2',
        'with_completed_price' => 'decimal:2',
        'return_order_target_price' => 'decimal:2',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }
}
