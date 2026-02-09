<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Reseller;

class ResellerOrderController extends OrderController
{
    /**
     * Show reseller specific order form.
     */
    public function create()
    {
        $products = Product::select('id', 'name', 'sku', 'selling_price', 'quantity')->get();
        // Get only resellers
        $resellers = Reseller::all();
        $cities = \App\Models\City::all();

        return view('orders.reseller_create', compact('products', 'resellers', 'cities'));
    }

    // Store uses OrderController@store but with reseller_id validation being critical
}
