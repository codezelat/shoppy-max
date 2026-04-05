<?php

namespace App\Exports;

use App\Models\Order;
use App\Support\CourierSettlement;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WaybillExcelExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly Collection $orders)
    {
    }

    public function collection(): Collection
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'Waybill ID',
            'Order ID',
            'Parcel Type',
            'Parcel Description',
            'Recipient Name',
            'Recipient Mobile1',
            'Recipient Mobile2',
            'Recipient Address',
            'Recipient City',
            'COD Amount',
            'Exchange',
        ];
    }

    public function map($order): array
    {
        /** @var Order $order */
        $customer = $order->customer;
        $primaryMobile = trim((string) ($order->customer_phone ?: ($customer->mobile ?? '')));
        $secondaryMobile = trim((string) ($customer->landline ?? ''));
        $recipientCity = trim((string) ($order->customer_city ?: ($order->city->city_name ?? '')));

        if ($secondaryMobile !== '' && $secondaryMobile === $primaryMobile) {
            $secondaryMobile = '';
        }

        return [
            (string) ($order->waybill_number ?? ''),
            (string) ($order->order_number ?? ''),
            1,
            (string) ($order->sales_note ?? ''),
            (string) ($order->customer_name ?: ($customer->name ?? '')),
            $primaryMobile,
            $secondaryMobile,
            (string) ($order->customer_address ?: ($customer->address ?? '')),
            $recipientCity,
            CourierSettlement::customerOutstandingAmount($order),
            '0',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_NUMBER_00,
            'K' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
