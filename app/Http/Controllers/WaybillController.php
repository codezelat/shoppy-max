<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Courier;
use App\Models\CourierWaybill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WaybillController extends Controller
{
    /**
     * Show Waybill Print Selection - List of Couriers
     */
    public function index()
    {
        $couriers = Courier::query()
            ->where('is_active', true)
            ->withCount([
                'orders as printable_orders_count' => function ($query) {
                    $this->applyPrintableOrderConstraints($query);
                },
                'waybills as available_waybills_count' => function ($query) {
                    $query->whereNull('order_id');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('orders.waybill.index', compact('couriers'));
    }
    
    /**
     * Show orders for a specific courier
     */
    public function show(Request $request, Courier $courier)
    {
        $availableWaybillsCount = $courier->waybills()->available()->count();
        if ($availableWaybillsCount < 1) {
            return redirect()
                ->route('orders.waybill.index')
                ->with('error', "Add waybill IDs for {$courier->name} before opening the print queue.");
        }

        $perPage = in_array((int) $request->input('per_page'), [25, 50, 100], true)
            ? (int) $request->input('per_page')
            : 25;

        $ordersQuery = Order::query()
            ->with('customer')
            ->where('courier_id', $courier->id);

        $this->applyPrintableOrderConstraints($ordersQuery);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $ordersQuery->where(function ($query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhere('waybill_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $ordersQuery->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $ordersQuery->whereDate('order_date', '<=', $request->date_to);
        }

        $orders = $ordersQuery
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $statsBaseQuery = Order::query()
            ->where('courier_id', $courier->id)
            ->where('call_status', 'confirm');

        $stats = [
            'eligible' => (clone $statsBaseQuery)
                ->where('delivery_status', 'pending')
                ->where(function ($waybillQuery) {
                    $waybillQuery->whereNull('waybill_number')
                        ->orWhere('waybill_number', '');
                })
                ->count(),
            'confirm_total' => (clone $statsBaseQuery)->where('delivery_status', 'pending')->count(),
            'with_waybill' => (clone $statsBaseQuery)
                ->where(function ($waybillQuery) {
                    $waybillQuery->whereNotNull('waybill_number')
                        ->where('waybill_number', '!=', '');
                })
                ->count(),
            'available_waybills' => $availableWaybillsCount,
            'waybill_shortfall' => max(((clone $statsBaseQuery)->where('delivery_status', 'pending')->count()) - $availableWaybillsCount, 0),
            'next_available_waybill' => $courier->waybills()->available()->orderBy('id')->value('code'),
        ];

        return view('orders.waybill.show', compact('courier', 'orders', 'stats'));
    }

    /**
     * Print selected waybills
     */
    public function print(Request $request)
    {
        $validated = $request->validate([
            'courier_id' => 'required|exists:couriers,id',
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'integer|exists:orders,id',
        ]);

        $orderIds = collect($request->input('order_ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
        $courierId = (int) $validated['courier_id'];

        try {
            $orders = DB::transaction(function () use ($orderIds, $courierId) {
                $ordersQuery = Order::query()
                    ->where('courier_id', $courierId)
                    ->whereIn('id', $orderIds)
                    ->with('items', 'city', 'customer')
                    ->lockForUpdate();

                $this->applyPrintableOrderConstraints($ordersQuery);

                $orders = $ordersQuery->get()
                    ->sortBy(fn (Order $order) => $orderIds->search($order->id))
                    ->values();

                if ($orders->count() !== $orderIds->count()) {
                    throw ValidationException::withMessages([
                        'order_ids' => 'Only call-confirmed orders with pending delivery and no waybill numbers can be printed.',
                    ]);
                }

                $availableWaybills = CourierWaybill::query()
                    ->where('courier_id', $courierId)
                    ->available()
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->limit($orderIds->count())
                    ->get()
                    ->values();

                if ($availableWaybills->count() < $orderIds->count()) {
                    throw ValidationException::withMessages([
                        'order_ids' => 'Not enough available waybill IDs for this courier. Add more waybill IDs before printing.',
                    ]);
                }

                $timestamp = now();

                foreach ($orders as $index => $order) {
                    $waybill = $availableWaybills[$index];

                    $order->waybill_number = $waybill->code;
                    $order->delivery_status = 'waybill_printed';
                    if (!$order->waybill_printed_at) {
                        $order->waybill_printed_at = $timestamp;
                    }
                    $order->save();

                    $waybill->order_id = $order->id;
                    $waybill->allocated_at = $timestamp;
                    $waybill->save();
                }

                return $orders;
            });
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first() ?: 'Unable to print waybills.');
        }

        return view('orders.waybill.print', compact('orders'));
    }

    private function applyPrintableOrderConstraints($query): void
    {
        $query->where('call_status', 'confirm')
            ->where('delivery_status', 'pending')
            ->where(function ($waybillQuery) {
                $waybillQuery->whereNull('waybill_number')
                    ->orWhere('waybill_number', '');
            });
    }
}
