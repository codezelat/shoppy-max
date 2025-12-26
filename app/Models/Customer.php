<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'business_name',
        'email',
        'mobile',
        'landline',
        'address',
        'country',
        'province',
        'district',
        'city',
    ];
}
