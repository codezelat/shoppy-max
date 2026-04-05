<?php

namespace App\Http\Controllers;

use App\Exports\WaybillExcelExport;
use App\Models\Courier;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class WaybillExcelExportController extends Controller
{
    public function index()
    {
        $couriers = Courier::query()
            ->where('is_active', true)
            ->withCount([
                'orders as printed_waybills_count' => function ($query) {
                    $this->applyPrintedWaybillConstraints($query);
                },
                'orders as pending_export_count' => function ($query) {
                    $this->applyPrintedWaybillConstraints($query);
                    $query->whereNull('waybill_excel_exported_at');
                },
                'orders as downloaded_export_count' => function ($query) {
                    $this->applyPrintedWaybillConstraints($query);
                    $query->whereNotNull('waybill_excel_exported_at');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('orders.waybill_excel.index', compact('couriers'));
    }

    public function show(Request $request, Courier $courier)
    {
        $this->validateFilters($request);

        $printedWaybillsCount = $courier->orders()
            ->where(function (Builder $query): void {
                $this->applyPrintedWaybillConstraints($query);
            })
            ->count();

        if ($printedWaybillsCount < 1) {
            return redirect()
                ->route('orders.waybill-excel.index')
                ->with('error', "No printed waybill orders are available for {$courier->name} yet.");
        }

        $perPage = in_array((int) $request->input('per_page'), [25, 50, 100], true)
            ? (int) $request->input('per_page')
            : 25;

        $orders = $this->buildFilteredOrdersQuery($request, $courier)
            ->with(['customer', 'city'])
            ->orderByDesc('waybill_printed_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $statsBaseQuery = Order::query()
            ->where('courier_id', $courier->id);

        $this->applyPrintedWaybillConstraints($statsBaseQuery);

        $stats = [
            'pending_total' => (clone $statsBaseQuery)->whereNull('waybill_excel_exported_at')->count(),
            'downloaded_total' => (clone $statsBaseQuery)->whereNotNull('waybill_excel_exported_at')->count(),
            'printed_total' => (clone $statsBaseQuery)->count(),
        ];

        return view('orders.waybill_excel.show', compact('courier', 'orders', 'stats'));
    }

    public function export(Request $request, Courier $courier)
    {
        $this->validateFilters($request);

        $orders = $this->buildFilteredOrdersQuery($request, $courier)
            ->with(['customer', 'city'])
            ->orderByDesc('waybill_printed_at')
            ->orderByDesc('id')
            ->get();

        if ($orders->isEmpty()) {
            return back()->with('error', 'No waybill orders match the current export filters.');
        }

        $timestamp = now();
        $pendingExportIds = $orders
            ->filter(fn (Order $order) => !$order->waybill_excel_exported_at)
            ->pluck('id');

        if ($pendingExportIds->isNotEmpty()) {
            Order::query()
                ->whereIn('id', $pendingExportIds)
                ->update([
                    'waybill_excel_exported_at' => $timestamp,
                    'waybill_excel_exported_by' => Auth::id(),
                    'updated_at' => $timestamp,
                ]);
        }

        $filename = 'waybill_excel_export_' . str($courier->name)->slug('_') . '_' . $timestamp->format('Ymd_His') . '.xlsx';

        return Excel::download(new WaybillExcelExport($orders), $filename);
    }

    private function buildFilteredOrdersQuery(Request $request, Courier $courier): Builder
    {
        $query = Order::query()
            ->where('courier_id', $courier->id);

        $this->applyPrintedWaybillConstraints($query);

        if (!$request->boolean('show_downloaded')) {
            $query->whereNull('waybill_excel_exported_at');
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('order_number', 'like', "%{$search}%")
                    ->orWhere('waybill_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhereHas('customer', function (Builder $customerQuery) use ($search): void {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%")
                            ->orWhere('landline', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('waybill_printed_at', $request->input('date'));
        }

        return $query;
    }

    private function applyPrintedWaybillConstraints(Builder $query): void
    {
        $query->where(function (Builder $builder): void {
            $builder->whereNotNull('waybill_printed_at')
                ->orWhere(function (Builder $waybillQuery): void {
                    $waybillQuery->whereNotNull('waybill_number')
                        ->where('waybill_number', '!=', '');
                });
        });
    }

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer'],
            'show_downloaded' => ['nullable', 'boolean'],
        ]);
    }
}
