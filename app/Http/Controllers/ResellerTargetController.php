<?php

namespace App\Http\Controllers;

use App\Models\ResellerTarget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResellerTargetController extends Controller
{
    /**
     * List all targets.
     */
    public function index(Request $request)
    {
        $query = ResellerTarget::with('user');

        if ($request->has('search')) {
            // Filter...
        }

        $targets = $query->latest()->paginate(10);

        return view('resellers.targets.index', compact('targets'));
    }

    /**
     * Show form to add target.
     */
    public function create()
    {
        $users = User::whereIn('user_type', ['reseller', 'direct_reseller', 'sub_user'])->get();
        return view('resellers.targets.create', compact('users'));
    }

    /**
     * Store a new target.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'target_type' => 'required|string',
            'target_completed_price' => 'nullable|numeric',
            'target_not_completed_price' => 'nullable|numeric',
            'return_order_target_price' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'ref_id' => 'nullable|string',
            'target_pieces_qty' => 'nullable|integer',
        ]);

        $target = new ResellerTarget($validated);
        $target->created_by = Auth::id();
        $target->save();

        return redirect()->route('resellers.targets.index')->with('success', 'Target created successfully.');
    }
}
