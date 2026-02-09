<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use App\Rules\SriLankaLandline;
use App\Rules\SriLankaMobile;
use Illuminate\Http\Request;

class ResellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Reseller::query();

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
        $allowedSorts = ['name', 'business_name', 'mobile', 'email', 'due_amount'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }
        if ($request->has('export')) {
            $resellers = $query->get();

            if ($request->input('export') === 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ResellersExport($resellers), 'resellers.xlsx');
            }

            if ($request->input('export') === 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.resellers_pdf', compact('resellers'));
                $pdf->setPaper('a4', 'landscape');

                return $pdf->stream('resellers.pdf');
            }
        }

        $resellers = $query->paginate(10);

        // Stats for Dashboard Cards
        $totalResellers = Reseller::count();
        $totalDue = Reseller::sum('due_amount');
        $activeResellers = Reseller::where('due_amount', '>', 0)->count(); // Example metric

        return view('contacts.resellers.index', compact('resellers', 'totalResellers', 'totalDue', 'activeResellers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = config('locations.countries');
        $slData = config('locations.sri_lanka');

        return view('contacts.resellers.create', compact('countries', 'slData'));
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
        $countries = config('locations.countries');
        $slData = config('locations.sri_lanka');

        return view('contacts.resellers.edit', compact('reseller', 'countries', 'slData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reseller $reseller)
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
