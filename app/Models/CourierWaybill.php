<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierWaybill extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_id',
        'code',
        'prefix',
        'sequence_number',
        'suffix',
        'range_start',
        'range_end',
        'order_id',
        'allocated_at',
    ];

    protected $casts = [
        'sequence_number' => 'integer',
        'range_start' => 'integer',
        'range_end' => 'integer',
        'allocated_at' => 'datetime',
    ];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id');
    }
}
