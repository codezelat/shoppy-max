<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reseller extends Model
{
    protected $fillable = [
        'business_name',
        'name',
        'mobile',
        'due_amount',
    ];
}
