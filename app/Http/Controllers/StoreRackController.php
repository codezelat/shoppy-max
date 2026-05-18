<?php

namespace App\Http\Controllers;

use App\Models\StoreRack;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StoreRackController extends Controller
{
    public function index(string $store)
    {
        $store = $this->validatedStore($store);
        $racks = StoreRack::query()
            ->where('store_type', $store)
            ->orderBy('row_key')
            ->paginate(20);

        return view('purchases.store-racks.index', [
            'store' => $store,
            'storeLabel' => StoreRack::storeLabel($store),
            'racks' => $racks,
        ]);
    }

    public function store(Request $request, string $store)
    {
        $store = $this->validatedStore($store);

        $validated = $request->validate([
            'row_name' => 'required|string|max:100',
        ]);

        $rowName = preg_replace('/\s+/', ' ', trim((string) $validated['row_name']));
        $rowKey = StoreRack::normalizeRowKey($rowName);

        if ($rowKey === '') {
            throw ValidationException::withMessages([
                'row_name' => 'Enter a valid rack row.',
            ]);
        }

        $exists = StoreRack::query()
            ->where('store_type', $store)
            ->where('row_key', $rowKey)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'row_name' => 'This rack row already exists for '.StoreRack::storeLabel($store).'.',
            ]);
        }

        StoreRack::create([
            'store_type' => $store,
            'row_name' => $rowName,
            'row_key' => $rowKey,
        ]);

        return back()->with('success', 'Rack row created.');
    }

    private function validatedStore(string $store): string
    {
        $store = StoreRack::normalizeStoreType($store);
        abort_unless(StoreRack::isValidStoreType($store), 404);

        return $store;
    }
}
