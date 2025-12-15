<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {
        $subCategories = SubCategory::with('category')->get();
        return view('product_management.sub_categories.index', compact('subCategories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('product_management.sub_categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
        ]);

        SubCategory::create($request->all());

        return redirect()->route('sub-categories.index')->with('success', 'Sub Category created successfully.');
    }

    public function edit(SubCategory $subCategory)
    {
        $categories = Category::all();
        return view('product_management.sub_categories.edit', compact('subCategory', 'categories'));
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
        ]);

        $subCategory->update($request->all());

        return redirect()->route('sub-categories.index')->with('success', 'Sub Category updated successfully.');
    }

    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();
        return redirect()->route('sub-categories.index')->with('success', 'Sub Category deleted successfully.');
    }
}
