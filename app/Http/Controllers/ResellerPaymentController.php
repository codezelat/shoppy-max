<?php

namespace App\Http\Controllers;

use App\Models\ResellerPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResellerPaymentController extends Controller
{
    /**
     * List user payments.
     */
    public function index(Request $request)
    {
        $payments = ResellerPayment::with('user')->latest()->paginate(10);
        return view('resellers.payments.index', compact('payments'));
    }

    /**
     * View due payments.
     */
    public function dues()
    {
        // Conceptual: Due = (Total Commission - Paid Amount)
        // Since we don't have Commission calculations yet, we'll list users with a placeholder 'due' calculation.
        // For now, this view will list all resellers.
        $users = User::whereIn('user_type', ['reseller', 'direct_reseller'])->paginate(10);
        return view('resellers.payments.dues', compact('users'));
    }
    
    // create and store methods for payments...
    public function create()
    {
        $users = User::whereIn('user_type', ['reseller', 'direct_reseller'])->get();
        return view('resellers.payments.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'payment_ref_no' => 'nullable|string',
        ]);

        $payment = new ResellerPayment($validated);
        $payment->created_by = Auth::id();
        $payment->save();

        return redirect()->route('resellers.payments.index')->with('success', 'Payment recorded.');
    }
}
