<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use Illuminate\Http\Request;

class ResellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $resellers = Reseller::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('business_name', 'like', "%{$search}%")
                         ->orWhere('mobile', 'like', "%{$search}%");
        })->paginate(10);

        return view('contacts.resellers.index', compact('resellers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('contacts.resellers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'due_amount' => 'numeric|min:0',
        ]);

        Reseller::create($request->all());

        return redirect()->route('resellers.index')->with('success', 'Reseller created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reseller $reseller)
    {
        return view('contacts.resellers.show', compact('reseller'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reseller $reseller)
    {
        return view('contacts.resellers.edit', compact('reseller'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reseller $reseller)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'due_amount' => 'numeric|min:0',
        ]);

        $reseller->update($request->all());

        return redirect()->route('resellers.index')->with('success', 'Reseller updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reseller $reseller)
    {
        $reseller->delete();

        return redirect()->route('resellers.index')->with('success', 'Reseller deleted successfully.');
    }
}
