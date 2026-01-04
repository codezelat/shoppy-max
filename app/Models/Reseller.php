<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reseller extends Model
{
    protected $fillable = [
        'business_name',
        'name',
        'email',
        'mobile',
        'landline',
        'address',
        'city',
        'district',
        'province',
        'country',
        'due_amount',
    ];

    public function targets()
    {
        return $this->hasMany(ResellerTarget::class);
    }

    public function payments()
    {
        return $this->hasMany(ResellerPayment::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
