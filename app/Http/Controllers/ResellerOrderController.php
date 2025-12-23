<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResellerOrderController extends OrderController
{
    /**
     * Show reseller specific order form.
     */
    public function create()
    {
        $products = Product::select('id', 'name', 'sku', 'selling_price', 'quantity')->get();
        // Get only resellers
        $resellers = User::whereIn('user_type', ['reseller', 'direct_reseller'])->get();
        $cities = \App\Models\City::all();
        
        return view('orders.reseller_create', compact('products', 'resellers', 'cities'));
    }
    
    // Store uses OrderController@store but with reseller_id validation being critical
}
