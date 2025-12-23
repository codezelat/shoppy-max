<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_type',
        'target_completed_price',
        'target_not_completed_price',
        'return_order_target_price',
        'start_date',
        'end_date',
        'ref_id',
        'target_pieces_qty',
        'created_by',
    ];

    protected $casts = [
        'target_completed_price' => 'decimal:2',
        'target_not_completed_price' => 'decimal:2',
        'return_order_target_price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
