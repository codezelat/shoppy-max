<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory; // Make sure SubCategory model is imported
use App\Models\Unit;
use App\Models\Attribute;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'unit'])->latest()->paginate(10);
        return view('product_management.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $subCategories = SubCategory::all(); // Fetch sub-categories
        $units = Unit::all();
        return view('product_management.products.create', compact('categories', 'subCategories', 'units')); // Pass subCategories to view
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'sku' => 'required|unique:products,sku',
            'category_id' => 'required',
            'selling_price' => 'required|numeric',
            'alert_quantity' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        // Barcode generation logic: For now we assume the SKU IS the barcode data
        // If we need to generate an actual image file, we can do it here using a library like milon/barcode
        // But the requirement says "barcode generated using SKU", so we store SKU as the reference.
        $validated['barcode_data'] = $request->sku; 
        
        $product = Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $subCategories = SubCategory::all(); // Fetch sub-categories
        $units = Unit::all();
        return view('product_management.products.edit', compact('product', 'categories', 'subCategories', 'units')); // Pass subCategories
    }

    public function update(Request $request, Product $product)
    {
        // Quantity is NOT updatable here as per requirements ("Stock cannot be adjusted manually")
        $validated = $request->validate([
            'name' => 'required',
            'sku' => 'required|unique:products,sku,' . $product->id,
            'category_id' => 'required',
            'selling_price' => 'required|numeric',
            'alert_quantity' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

         $validated['barcode_data'] = $request->sku;

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
