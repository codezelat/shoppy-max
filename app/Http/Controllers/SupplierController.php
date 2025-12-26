<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Rules\SriLankaMobile;
use App\Rules\SriLankaLandline;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Supplier::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $allowedSorts = ['name', 'business_name', 'mobile', 'email'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }
        if ($request->has('export')) {
            $suppliers = $query->get();
            
            if ($request->input('export') === 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SuppliersExport($suppliers), 'suppliers.xlsx');
            }
            
            if ($request->input('export') === 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.suppliers_pdf', compact('suppliers'));
                $pdf->setPaper('a4', 'landscape');
                return $pdf->stream('suppliers.pdf');
            }
        }

        $suppliers = $query->paginate(10);

        return view('contacts.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = config('locations.countries');
        $slData = config('locations.sri_lanka');
        return view('contacts.suppliers.create', compact('countries', 'slData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => ['required', 'string', new SriLankaMobile],
            'landline' => ['nullable', 'string', new SriLankaLandline],
            'address' => 'required|string',
            'country' => 'required|string',
            'province' => 'nullable|string',
            'district' => 'nullable|string',
            'city' => 'nullable|string',
            'due_amount' => 'numeric|min:0',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return view('contacts.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        $countries = config('locations.countries');
        $slData = config('locations.sri_lanka');
        return view('contacts.suppliers.edit', compact('supplier', 'countries', 'slData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => ['required', 'string', new SriLankaMobile],
            'landline' => ['nullable', 'string', new SriLankaLandline],
            'address' => 'required|string',
            'country' => 'required|string',
            'province' => 'nullable|string',
            'district' => 'nullable|string',
            'city' => 'nullable|string',
            'due_amount' => 'numeric|min:0',
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
