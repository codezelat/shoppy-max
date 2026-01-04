<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Placeholder Stats (Replace with actual queries later)
        // In a real app, you'd aggregate Order models where user_id = $user->id
        $stats = [
            'total_sales_count' => 0,
            'total_sales_value' => 0,
            'pending_orders' => 0,
            'confirmed_orders' => 0,
            'hold_orders' => 0,
            'total_commission' => 0,
            'paid_commission' => 0,
        ];

        return view('dashboard', compact('user', 'stats'));
    }
}
