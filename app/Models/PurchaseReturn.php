<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'user_id',
        'quantity',
        'reason',
        'returned_at',
    ];

    protected $casts = [
        'returned_at' => 'datetime',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
