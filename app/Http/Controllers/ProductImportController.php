<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\ProductVariant;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductTemplateExport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductImportController extends Controller
{
    public function show()
    {
        return view('product_management.products.import');
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProductTemplateExport, 'product_import_template.xlsx');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $data = Excel::toArray([], $request->file('file'));

        if (empty($data) || empty($data[0])) {
            return back()->with('error', 'The file is empty or invalid.');
        }

        $rows = array_slice($data[0], 1); // Skip header
        $previewData = [];
        $validRowsCount = 0;
        $hasErrors = false;

        // Pre-fetch related data for quick validation/lookup
        $categories = Category::all()->keyBy(function($item) { return strtolower($item->name); });
        $subCategories = SubCategory::all()->keyBy(function($item) { return strtolower($item->name); });
        $units = Unit::all()->keyBy(function($item) { return strtolower($item->name); });
        
        $existingSkus = ProductVariant::pluck('sku')->map(fn($sku) => strtolower($sku))->toArray();
        $fileSkus = [];
        
        // Grouping Logic: We process rows but need to identify products
        // In this preview, we just list flattened variants but attach product-level flags

        foreach ($rows as $index => $row) {
             // Columns: 
             // 0=Name, 1=Cat, 2=SubCat, 3=Desc, 4=Unit, 5=Value, 6=SKU, 7=Price, 8=Limit, 9=Qty, 10=Alert, 11=ImageURL
             if (empty($row[0])) continue;

             $name = trim($row[0]);
             $catName = isset($row[1]) ? trim($row[1]) : null;
             $subCatName = isset($row[2]) ? trim($row[2]) : null;
             $desc = isset($row[3]) ? trim($row[3]) : null;
             $unitName = isset($row[4]) ? trim($row[4]) : null;
             $unitValue = isset($row[5]) ? trim($row[5]) : null;
             $sku = isset($row[6]) ? trim($row[6]) : null;
             $price = isset($row[7]) ? (float) $row[7] : 0;
             $limit = isset($row[8]) ? (float) $row[8] : null;
             $qty = isset($row[9]) ? (int) $row[9] : 0;
             $alert = isset($row[10]) ? (int) $row[10] : 0;
             $imageUrl = isset($row[11]) ? trim($row[11]) : null;

             $errors = [];
             $rowStatus = 'OK'; // OK, ERROR, MISSING_DATA

             // 1. Validation: Category
             $catId = null;
             if (!$catName) {
                 $errors['category'] = "Required";
             } else {
                 $key = strtolower($catName);
                 if (isset($categories[$key])) {
                     $catId = $categories[$key]->id;
                 } else {
                     $errors['category'] = "MISSING_CATEGORY"; // Special code for UI
                 }
             }

             // 2. Validation: Sub Category
             $subCatId = null;
             if ($subCatName) {
                 $key = strtolower($subCatName);
                 if (isset($subCategories[$key])) {
                     $subCatObj = $subCategories[$key];
                     if ($catId && $subCatObj->category_id != $catId) {
                         $errors['sub_category'] = "Mismatch"; 
                     } else {
                         $subCatId = $subCatObj->id;
                     }
                 } else {
                     $errors['sub_category'] = "MISSING_SUB_CATEGORY";
                 }
             }

             // 3. Validation: Unit
             $unitId = null;
             if (!$unitName) {
                 $errors['unit'] = "Required";
             } else {
                 $key = strtolower($unitName);
                 if (isset($units[$key])) {
                     $unitId = $units[$key]->id;
                 } else {
                      $errors['unit'] = "MISSING_UNIT";
                 }
             }

             // 4. Validation: SKU
             if (!$sku) {
                 $errors['sku'] = "Required";
             } else {
                 $skuLower = strtolower($sku);
                 if (in_array($skuLower, $existingSkus)) {
                     $errors['sku'] = "Exists in DB";
                 } elseif (in_array($skuLower, $fileSkus)) {
                     $errors['sku'] = "Duplicate in File";
                 }
                 $fileSkus[] = $skuLower;
             }

             // 5. Validation: Price & Qty
             if ($price <= 0) $errors['price'] = "Invalid";
             if ($qty < 0) $errors['qty'] = "Invalid";
             
             // Check if only "Missing" errors exist or actual logic errors
             if (!empty($errors)) {
                 $hasErrors = true;
             } else {
                 $validRowsCount++;
             }

             $previewData[] = [
                 'row_id' => $index, // for tracking
                 'name' => $name,
                 'category_id' => $catId,
                 'category_name' => $catName,
                 'sub_category_id' => $subCatId,
                 'sub_category_name' => $subCatName,
                 'description' => $desc,
                 'unit_id' => $unitId,
                 'unit_name' => $unitName,
                 'unit_value' => $unitValue,
                 'sku' => $sku,
                 'selling_price' => $price,
                 'limit_price' => $limit,
                 'quantity' => $qty,
                 'alert_quantity' => $alert,
                 'image_url' => $imageUrl,
                 'errors' => $errors
             ];
        }

        session(['product_import_preview_data' => $previewData]);

        return view('product_management.products.import', compact('previewData', 'validRowsCount', 'hasErrors'));
    }

    public function store(Request $request)
    {
        $previewData = session('product_import_preview_data');

        if (!$previewData) {
            return redirect()->route('products.import.show')->with('error', 'Session expired. Please upload again.');
        }

        $count = 0;
        
        // Regroup by Product Name to avoid creating duplicate products if rows are scrambled
        // (Though usually file is sorted, better safe)
        $groupedData = collect($previewData)->groupBy('name');

        DB::transaction(function () use ($groupedData, &$count) {
            // Optimization: Pre-fetch products to avoid N+1 queries
            $productNames = $groupedData->keys();
            $existingProducts = Product::whereIn('name', $productNames)->get();

            // Map by lowercase name for case-insensitive lookup
            $productMap = [];
            foreach ($existingProducts as $p) {
                $productMap[strtolower($p->name)] = $p;
            }

            foreach ($groupedData as $productName => $variants) {
                // Use the FIRST valid row's creation data for the product
                // Find a row that has valid product data? Or just take the first one?
                $firstRow = $variants->first();
                
                // Skip if critical product errors exist?
                // Actually we rely on UI to block import if errors exist. 
                // But if user skips invalid rows, we proceed with valid ones.
                
                // 1. Find or Create Product
                $lowerName = strtolower($productName);
                $product = $productMap[$lowerName] ?? null;

                if (!$product) {
                    $image = null;
                    if (!empty($firstRow['image_url'])) {
                        // Attempt upload
                        try {
                             $image = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload($firstRow['image_url'], ['verify' => false])['secure_url'];
                        } catch (\Exception $e) {
                            // Ignore image error, continue creation?
                        }
                    }

                    $product = Product::create([
                        'name' => $productName,
                        'category_id' => $firstRow['category_id'],
                        'sub_category_id' => $firstRow['sub_category_id'],
                        'description' => $firstRow['description'],
                        'image' => $image,
                    ]);

                    // Add to map for subsequent lookups in this loop (if any)
                    $productMap[$lowerName] = $product;
                }

                // 2. Add Variants
                foreach ($variants as $row) {
                    if (!empty($row['errors'])) continue;

                    // Check duplicate SKU again to be safe
                    if (ProductVariant::where('sku', $row['sku'])->exists()) continue;

                    $variantImage = null; // Currently template supports 1 image per row, usually mapping to Product Image. 
                    // If user provides specific variant image logic, we'd need another column. 
                    // For now, let's assume image_url on row applies to Product if new, or ignored if variant?
                    // "Expert" decision: If product exists, maybe update image? No, safer to leave.
                    // Let's assume URL is for the PRODUCT.

                    $product->variants()->create([
                        'unit_id' => $row['unit_id'],
                        'unit_value' => $row['unit_value'],
                        'sku' => $row['sku'],
                        'selling_price' => $row['selling_price'],
                        'limit_price' => $row['limit_price'],
                        'quantity' => $row['quantity'],
                        'alert_quantity' => $row['alert_quantity'],
                        // 'image' => ... // Variant specific image not in simple template yet
                    ]);
                    $count++;
                }
            }
        });

        session()->forget('product_import_preview_data');

        return redirect()->route('products.index')->with('success', "Successfully processed $count variants.");
    }
}
