<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /**
     * Display a listing of couriers.
     */
    public function index()
    {
        $couriers = Courier::all();
        return view('couriers.index', compact('couriers'));
    }

    /**
     * Store a newly created courier in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'rates' => 'nullable|string',
        ]);

        Courier::create($validated);

        return redirect()->route('couriers.index')->with('success', 'Courier added successfully.');
    }

    /**
     * Update the specified courier in storage.
     */
    public function update(Request $request, Courier $courier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'rates' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $courier->update($validated);

        return redirect()->route('couriers.index')->with('success', 'Courier updated successfully.');
    }

    /**
     * Remove the specified courier from storage.
     */
    public function destroy(Courier $courier)
    {
        // Check for orders
        if ($courier->orders()->exists()) {
             return back()->with('error', 'Cannot delete courier with associated orders. Deactivate instead.');
        }
        
        $courier->delete();

        return redirect()->route('couriers.index')->with('success', 'Courier deleted successfully.');
    }
}
