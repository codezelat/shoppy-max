<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Reseller;

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

        $resellerAccount = Reseller::query()
            ->with('userAccount')
            ->where('user_id', $user->id)
            ->first();

        $resellerStats = null;
        if ($resellerAccount) {
            $orders = Order::query()->where('reseller_id', $resellerAccount->id);
            $resellerStats = [
                'label' => $resellerAccount->reseller_type === Reseller::TYPE_DIRECT_RESELLER
                    ? 'Direct Reseller Account'
                    : 'Reseller Account',
                'total_orders' => (clone $orders)->count(),
                'pending_orders' => (clone $orders)->where('status', 'pending')->count(),
                'confirmed_orders' => (clone $orders)->where('status', 'confirm')->count(),
                'delivered_orders' => (clone $orders)->where('delivery_status', 'delivered')->count(),
                'returned_orders' => (clone $orders)->where('delivery_status', 'returned')->count(),
            ];
        }

        return view('dashboard', compact('user', 'stats', 'resellerAccount', 'resellerStats'));
    }
}
