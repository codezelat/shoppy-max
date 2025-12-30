<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SellerTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SellerManagementController extends Controller
{
    /**
     * Display the seller dashboard.
     */
    public function dashboard(Request $request)
    {
        // For filtering by seller
        $sellers = User::where('user_type', 'seller')->get();
        
        $selectedSellerId = $request->input('seller_id');
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // Placeholder stats
        $stats = [
            'total_sales_count' => 0,
            'total_sales_value' => 0,
            'pending_orders' => 0,
            'confirmed_orders' => 0,
            'hold_orders' => 0,
            // 'total_commission' => 0, // Sellers do not have commission
        ];
        
        // Fetch Target if a seller is selected
        $target = null;
        $targetProgress = null;

        if ($selectedSellerId) {
            $target = SellerTarget::where('user_id', $selectedSellerId)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->latest()
                ->first();

            if ($target) {
                 $targetProgress = [
                    'sales_amount' => $stats['total_sales_value'], // Using placeholder stats for now
                    'target_amount' => $target->target_completed_price,
                    'percentage' => $target->target_completed_price > 0 ? min(100, ($stats['total_sales_value'] / $target->target_completed_price) * 100) : 0,
                ];
            }
        }
        
        return view('sellers.dashboard', compact('sellers', 'selectedSellerId', 'startDate', 'endDate', 'stats', 'target', 'targetProgress'));
    }

    /**
     * List sellers.
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 'seller');
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10);

        $type = 'seller';
        return view('sellers.users.index', compact('users', 'type'));
    }

    /**
     * Show form to create a new seller.
     */
    public function create()
    {
        $roles = Role::all();
        return view('sellers.users.create', compact('roles'));
    }

    /**
     * Store a new seller.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|confirmed|min:8',
            'branch' => 'nullable|string',
            'return_fee' => 'nullable|numeric',
            'courier_id' => 'nullable',
            'roles' => 'nullable|array',
            'parent_id' => 'nullable|exists:users,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'user_type' => 'seller', // Force user_type to seller
            'branch' => $validated['branch'],
            'return_fee' => $validated['return_fee'],
            'courier_id' => $validated['courier_id'],
            'parent_id' => $validated['parent_id'],
        ]);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()->route('sellers.users.index')
                         ->with('success', 'Seller created successfully.');
    }
}
