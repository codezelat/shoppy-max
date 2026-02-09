<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = \App\Models\ProductVariant::query()->with(['product.category', 'product.subCategory', 'unit']);

        // Check for specific product IDs (Bulk Export Selected)
        if (isset($this->request['product_ids']) && $this->request['product_ids']) {
            $ids = explode(',', $this->request['product_ids']);
            $query->whereIn('product_id', $ids);
        }

        if (isset($this->request['search']) && $this->request['search']) {
            $search = $this->request['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('barcode_data', 'like', "%{$search}%");
                })
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (isset($this->request['category_id']) && $this->request['category_id']) {
            $query->whereHas('product', function ($q) {
                $q->where('category_id', $this->request['category_id']);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Variant ID',
            'Product Name',
            'Category',
            'Sub Category',
            'Unit',
            'SKU',
            'Selling Price',
            'Limit Price',
            'Quantity',
            'Alert Quantity',
            'Product Created At',
        ];
    }

    public function map($variant): array
    {
        return [
            $variant->product->id,
            $variant->id,
            $variant->product->name,
            $variant->product->category->name ?? '',
            $variant->product->subCategory->name ?? '',
            $variant->unit->name ?? '',
            $variant->sku,
            number_format($variant->selling_price, 2),
            number_format($variant->limit_price, 2),
            $variant->quantity,
            $variant->alert_quantity,
            $variant->product->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
