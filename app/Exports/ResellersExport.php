<?php

namespace App\Exports;

use App\Models\Reseller;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResellersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $resellers;

    public function __construct($resellers)
    {
        $this->resellers = $resellers;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->resellers;
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
            'Due Amount',
            'Created At',
        ];
    }

    public function map($reseller): array
    {
        return [
            $reseller->id,
            $reseller->name,
            $reseller->business_name,
            $reseller->email,
            $reseller->mobile,
            $reseller->landline,
            $reseller->address,
            $reseller->city,
            $reseller->district,
            $reseller->province,
            $reseller->country,
            number_format($reseller->due_amount, 2),
            $reseller->created_at->format('Y-m-d'),
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
