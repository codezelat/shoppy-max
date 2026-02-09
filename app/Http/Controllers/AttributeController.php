<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('values')->get();

        return view('product_management.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('product_management.attributes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'values' => 'nullable|string', // Comma separated values
        ]);

        $attribute = Attribute::create(['name' => $request->name]);

        if ($request->values) {
            $values = explode(',', $request->values);
            foreach ($values as $value) {
                AttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'value' => trim($value),
                ]);
            }
        }

        return redirect()->route('attributes.index')->with('success', 'Attribute created successfully.');
    }

    public function edit(Attribute $attribute)
    {
        return view('product_management.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required',
            // Update values logic can be complex (add/remove), keeping simple name update for now
        ]);

        $attribute->update(['name' => $request->name]);

        return redirect()->route('attributes.index')->with('success', 'Attribute updated successfully.');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();

        return redirect()->route('attributes.index')->with('success', 'Attribute deleted successfully.');
    }
}
