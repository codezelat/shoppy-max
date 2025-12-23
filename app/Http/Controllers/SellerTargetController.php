<?php

namespace App\Http\Controllers;

use App\Models\SellerTarget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerTargetController extends Controller
{
    /**
     * List all targets.
     */
    public function index(Request $request)
    {
        $query = SellerTarget::with('user');

        if ($request->has('search')) {
             $search = $request->input('search');
             $query->whereHas('user', function($q) use ($search) {
                 $q->where('name', 'like', "%{$search}%");
             });
        }

        $targets = $query->latest()->paginate(10);

        return view('sellers.targets.index', compact('targets'));
    }

    /**
     * Show form to add target.
     */
    public function create()
    {
        $users = User::where('user_type', 'seller')->get();
        return view('sellers.targets.create', compact('users'));
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

        $target = new SellerTarget($validated);
        $target->created_by = Auth::id();
        $target->save();

        return redirect()->route('sellers.targets.index')->with('success', 'Target created successfully.');
    }
}
