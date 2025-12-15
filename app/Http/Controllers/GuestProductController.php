<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class GuestProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'unit'])->latest()->paginate(12);
        return view('guest.products', compact('products'));
    }
}
