<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ResellerManagementController extends Controller
{
    /**
     * Display the reseller dashboard.
     */
    public function dashboard(Request $request)
    {
        // For filtering by reseller
        $resellers = User::whereIn('user_type', ['reseller', 'direct_reseller'])->get();
        
        $selectedResellerId = $request->input('reseller_id');
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // Placeholder stats (To be connected to Orders module later)
        $stats = [
            'total_sales_count' => 0,
            'total_sales_value' => 0,
            'pending_orders' => 0,
            'confirmed_orders' => 0,
            'hold_orders' => 0,
            'total_commission' => 0,
            'total_delivered_commission' => 0,
            'paid_commission' => 0,
        ];
        
        // If an Orders module exists, query here based on $selectedResellerId and dates
        
        return view('resellers.dashboard', compact('resellers', 'selectedResellerId', 'startDate', 'endDate', 'stats'));
    }

    /**
     * List users (New Resellers, Sub Users, etc.)
     * filtered by type if provided.
     */
    public function index(Request $request)
    {
        $type = $request->input('type'); // 'reseller', 'sub_user', 'direct_reseller'
        
        $query = User::query();

        if ($type) {
            $query->where('user_type', $type);
        } else {
             // If no type, maybe show all related to reseller management
             $query->whereIn('user_type', ['reseller', 'direct_reseller', 'sub_user']);
        }
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%") // Assuming username is a field or just use name/email as username
                  ->orWhere('name', 'like', "%{$search}%"); // Using name as fallback for username
            });
        }

        $users = $query->paginate(10);

        return view('resellers.users.index', compact('users', 'type'));
    }

    /**
     * Show form to create a new user.
     */
    public function create()
    {
        $roles = Role::all();
        // Assuming we might want to list parent resellers for sub-users
        $resellers = User::whereIn('user_type', ['reseller', 'direct_reseller'])->get();
        return view('resellers.users.create', compact('roles', 'resellers'));
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|confirmed|min:8',
            'user_type' => 'required|string', // reseller, direct_reseller, sub_user
            'branch' => 'nullable|string',
            'return_fee' => 'nullable|numeric',
            'courier_id' => 'nullable', // integer/string?
            'roles' => 'nullable|array',
            'parent_id' => 'nullable|exists:users,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'user_type' => $validated['user_type'],
            'branch' => $validated['branch'],
            'return_fee' => $validated['return_fee'],
            'courier_id' => $validated['courier_id'],
            'parent_id' => $validated['parent_id'],
        ]);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()->route('resellers.users.index', ['type' => $validated['user_type']])
                         ->with('success', 'User created successfully.');
    }
    
    // Add edit/update/destroy methods as needed...
}
