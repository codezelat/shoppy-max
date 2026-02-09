<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CitiesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $cities;

    public function __construct($cities)
    {
        $this->cities = $cities;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->cities;
    }

    public function headings(): array
    {
        return [
            'ID',
            'City Name',
            'Postal Code',
            'District',
            'Province',
            'Created At',
        ];
    }

    public function map($city): array
    {
        return [
            $city->id,
            $city->city_name,
            $city->postal_code,
            $city->district,
            $city->province,
            $city->created_at->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
