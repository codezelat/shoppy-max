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
}
