<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use PDF; // Assuming a PDF wrapper or using a view for print

class WaybillController extends Controller
{
    /**
     * Show Waybill Print Selection.
     */
    public function index()
    {
        // Orders ready for waybill (e.g., confirmed but not yet printed, or any confirmed)
        $orders = Order::where('status', 'confirmed')->latest()->paginate(20);
        return view('orders.waybill.index', compact('orders'));
    }
    
    /**
     * Print selected waybills (A4 4-up).
     */
    public function print(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
        
        if (empty($orderIds)) {
            return back()->with('error', 'No orders selected.');
        }
        
        $orders = Order::whereIn('id', $orderIds)->with('items', 'city')->get();
        
        // Generate unique waybill numbers if missing
        foreach ($orders as $order) {
            if (!$order->waybill_number) {
                 $order->waybill_number = 'WB-' . $order->order_number;
                 $order->save();
            }
        }
        
        return view('orders.waybill.print', compact('orders'));
    }
}
