<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id',
        'amount',
        'payment_method',
        'reference_id',
        'status',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }
}
