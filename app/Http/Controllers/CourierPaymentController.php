<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\CourierPayment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourierPaymentController extends Controller
{
    /**
     * Display a listing of courier payments.
     */
    public function index()
    {
        $payments = CourierPayment::with('courier', 'orders')->latest()->paginate(20);
        return view('couriers.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new courier payment (Selecting Orders).
     */
    public function create(Request $request)
    {
        $couriers = Courier::where('is_active', true)->get();
        $selectedCourierId = $request->query('courier_id');
        
        $orders = collect();
        if ($selectedCourierId) {
            // Fetch orders delivered by this courier that haven't been "paid" (linked to payment) yet.
            // Assumption: Payment to courier is for "Delivered" orders usually? 
            // Or maybe "Dispatched" if checking remittance?
            // Requirement says "Receive Courier Payment" -> Form to log payments RECEIVED FROM couriers (COD Remittance).
            // So status should generally be 'delivered' (or whichever status implies COD money is collected).
            // For now, let's fetch 'delivered' or 'dispatched' orders not yet reconciled.
            
             $orders = Order::where('courier_id', $selectedCourierId)
                           ->whereNull('courier_payment_id')
                           ->whereIn('status', ['dispatched', 'delivered']) 
                           ->where('payment_method', 'cod') // Remittance usually only for COD
                           ->orderBy('created_at')
                           ->get();
        }

        return view('couriers.payments.create', compact('couriers', 'selectedCourierId', 'orders'));
    }

    /**
     * Store a newly created courier payment and update orders.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'courier_id' => 'required|exists:couriers,id',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string',
            'orders' => 'required|array', // Selected orders
            'orders.*.id' => 'required|exists:orders,id',
            'orders.*.courier_cost' => 'nullable|numeric', // Real cost input
            'orders.*.delivery_fee' => 'nullable|numeric', // Charged cost input
        ]);

        DB::beginTransaction();
        try {
            // Create Payment Record
            $payment = CourierPayment::create([
                'courier_id' => $validated['courier_id'],
                'user_id' => Auth::id(),
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'reference_number' => $validated['reference_number'],
            ]);

            // Update Orders
            foreach ($validated['orders'] as $orderData) {
                $order = Order::find($orderData['id']);
                
                // Allow entering/correcting costs at this stage
                if (isset($orderData['courier_cost'])) {
                    $order->courier_cost = $orderData['courier_cost'];
                }
                if (isset($orderData['delivery_fee'])) {
                    $order->delivery_fee = $orderData['delivery_fee'];
                }
                
                $order->courier_payment_id = $payment->id;
                
                // If this is remittance, maybe update payment_status to 'paid'?
                // Implicit logic: receiving money from courier means order is paid.
                if ($order->payment_method == 'cod') {
                    $order->payment_status = 'paid';
                }
                
                $order->save();
            }

            DB::commit();
            return redirect()->route('courier-payments.index')->with('success', 'Courier payment recorded and orders updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified payment (Rollback).
     */
    public function destroy(CourierPayment $courierPayment)
    {
        DB::beginTransaction();
        try {
            // Unlink orders
            foreach ($courierPayment->orders as $order) {
                $order->courier_payment_id = null;
                // Optional: Revert payment status? might be risky if paid elsewhere, leaving as is for now or clarify.
                // Safest is to just unlink so they reappear in the list.
                $order->save();
            }
            
            $courierPayment->delete();
            
            DB::commit();
            return redirect()->route('courier-payments.index')->with('success', 'Payment deleted and orders unlinked.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting payment: ' . $e->getMessage());
        }
    }
}
