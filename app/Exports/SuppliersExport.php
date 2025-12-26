<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuppliersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $suppliers;

    public function __construct($suppliers)
    {
        $this->suppliers = $suppliers;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->suppliers;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Business Name',
            'Email',
            'Mobile',
            'Landline',
            'Address',
            'City',
            'District',
            'Province',
            'Country',
            'Created At',
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->name,
            $supplier->business_name,
            $supplier->email,
            $supplier->mobile,
            $supplier->landline,
            $supplier->address,
            $supplier->city,
            $supplier->district,
            $supplier->province,
            $supplier->country,
            $supplier->created_at->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
