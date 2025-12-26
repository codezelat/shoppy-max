<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Rules\SriLankaMobile;
use App\Rules\SriLankaLandline;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'name'); // Default sort by name
        $direction = $request->input('direction', 'asc');

        $query = Customer::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Validate sort column to prevent SQL injection
        $allowedSorts = ['name', 'business_name', 'mobile', 'email', 'city', 'country'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }

        if ($request->has('export')) {
            $customers = $query->get();
            
            if ($request->input('export') === 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CustomersExport($customers), 'customers.xlsx');
            }
            
            if ($request->input('export') === 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.customers_pdf', compact('customers'));
                $pdf->setPaper('a4', 'landscape');
                return $pdf->stream('customers.pdf');
            }
        }

        $customers = $query->paginate(10);

        return view('contacts.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = config('locations.countries');
        $slData = config('locations.sri_lanka');
        return view('contacts.customers.create', compact('countries', 'slData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => ['required', 'string', new SriLankaMobile],
            'landline' => ['nullable', 'string', new SriLankaLandline],
            'address' => 'required|string',
            'country' => 'required|string',
            'province' => 'nullable|string',
            'district' => 'nullable|string',
            'city' => 'nullable|string',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return view('contacts.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $countries = config('locations.countries');
        $slData = config('locations.sri_lanka');
        return view('contacts.customers.edit', compact('customer', 'countries', 'slData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => ['required', 'string', new SriLankaMobile],
            'landline' => ['nullable', 'string', new SriLankaLandline],
            'address' => 'required|string',
            'country' => 'required|string',
            'province' => 'nullable|string',
            'district' => 'nullable|string',
            'city' => 'nullable|string',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
