<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuickCreateController extends Controller
{
    public function storeCategory(Request $request)
    {


        // Auto-generate code if not provided (though validator required it, we should probably make it optional in validation or generate before)
        // Wait, validator says REQUIRED. So I must change validation rule or send it from JS.
        // Better: Make it optional, generate if missing.
        
        $data = $request->all();
        // Since we changed logic, let's remove validation for code IF we generate it. 
        // ACTUALLY, let's just create a new Validator instance with modified rules or better yet, merge input.
        
        // Revised Logic:
        $data['code'] = $request->code ?? \Illuminate\Support\Str::slug($request->name);
        
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:categories,code',
        ]);

        if ($validator->fails()) {
             return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $category = Category::create($data);

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Category created successfully.'
        ]);
    }

    public function storeSubCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subCategory = SubCategory::create($request->all());
        // Load category for display if needed, though usually just ID/Name is enough
        $subCategory->load('category');

        return response()->json([
            'success' => true,
            'subCategory' => $subCategory,
            'message' => 'Sub Category created successfully.'
        ]);
    }

    public function storeUnit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $unit = Unit::create($request->all());

        return response()->json([
            'success' => true,
            'unit' => $unit,
            'message' => 'Unit created successfully.'
        ]);
    }
}
