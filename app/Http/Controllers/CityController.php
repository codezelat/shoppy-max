<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = City::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('city_name', 'like', "%{$search}%")
                    ->orWhere('postal_code', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'city_name');
        $direction = $request->input('direction', 'asc');
        $allowedSorts = ['city_name', 'postal_code', 'district'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('city_name', 'asc');
        }

        if ($request->has('export')) {
            $cities = $query->get();

            if ($request->input('export') === 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CitiesExport($cities), 'cities.xlsx');
            }

            if ($request->input('export') === 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.cities_pdf', compact('cities'));

                return $pdf->stream('cities.pdf');
            }
        }

        $cities = $query->paginate(10);

        return view('contacts.cities.index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $slData = config('locations.sri_lanka');

        return view('contacts.cities.create', compact('slData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city_name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
        ]);

        City::create($request->all());

        return redirect()->route('cities.index')->with('success', 'City created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        return view('contacts.cities.show', compact('city'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        $slData = config('locations.sri_lanka');

        return view('contacts.cities.edit', compact('city', 'slData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        $request->validate([
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city_name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
        ]);

        $city->update($request->all());

        return redirect()->route('cities.index')->with('success', 'City updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('cities.index')->with('success', 'City deleted successfully.');
    }

    /**
     * Get cities by district for API.
     */
    public function getCitiesByDistrict(Request $request)
    {
        $district = $request->input('district');

        if (! $district) {
            return response()->json([]);
        }

        $cities = City::where('district', $district)
            ->orderBy('city_name')
            ->get(['city_name', 'postal_code']);

        return response()->json($cities);
    }
}
