<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SellerTarget;

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

        // Fetch active target for the user
        // Assuming the latest target covering today's date
        $target = SellerTarget::where('user_id', $user->id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest()
            ->first();

        // Calculate progress percentages
        $targetProgress = [
            'sales_amount' => 0,
            'target_amount' => $target ? $target->target_completed_price : 0,
            'percentage' => 0,
        ];

        if ($target && $target->target_completed_price > 0) {
            // For now, using the placeholder sales value. 
            // In real implementation, this comes from Order aggregations.
            $targetProgress['sales_amount'] = $stats['total_sales_value']; 
            $targetProgress['percentage'] = min(100, ($targetProgress['sales_amount'] / $target->target_completed_price) * 100);
        }

        return view('dashboard', compact('user', 'stats', 'target', 'targetProgress'));
    }
}
