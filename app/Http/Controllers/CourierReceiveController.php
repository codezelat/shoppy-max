<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Order;
use App\Models\CourierPayment;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourierReceiveController extends Controller
{
    /**
     * Display the courier selection popup/page.
     */
    public function index()
    {
        $couriers = Courier::where('is_active', true)->orderBy('name')->get();
        return view('couriers.receive.index', compact('couriers'));
    }

    /**
     * Show the Receive Courier Payment form for a specific courier.
     */
    public function show(Courier $courier)
    {
        // Get eligible orders for this courier and not yet linked to a courier payment
        $orders = $this->eligibleOrdersQuery($courier)
            ->latest()
            ->take(50) // Limit for initial load
            ->get();

        $bankAccounts = BankAccount::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('couriers.receive.show', compact('courier', 'orders', 'bankAccounts'));
    }

    /**
     * Search for an order by Waybill/Order No.
     */
    public function searchOrder(Request $request) 
    {
        $query = $request->get('query');
        $courierId = $request->integer('courier_id');

        $orderQuery = Order::where(function($q) use ($query) {
                $q->where('waybill_number', $query)
                  ->orWhere('id', $query);
            });

        if ($courierId) {
            $orderQuery->where('courier_id', $courierId)
                ->where('status', 'confirm')
                ->whereNull('courier_payment_id');
        }

        $order = $orderQuery
            ->with(['customer', 'city', 'courier']) // Eager load relationships
            ->first();

        if ($order) {
            return response()->json([
                'success' => true,
                'data' => [
                    'waybill_number' => $order->waybill_number,
                    'order_no' => $order->id, // or order_number field
                    'customer_name' => $order->customer_name ?? $order->customer->name ?? 'N/A', // Fallback
                    'destination' => $order->city ? $order->city->name : ($order->customer_city ?? 'N/A'),
                    'description' => 'Order #' . $order->id, // Placeholder
                    'phone1' => $order->customer_phone,
                    'phone2' => $order->customer_phone_2 ?? '', // Assuming logic
                    'delivery_fee' => $order->delivery_fee,
                    'amount' => $order->total_amount,
                    'city' => $order->city ? $order->city->name : '',
                    'remarks' => $order->sales_note,
                    'id' => $order->id
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Order not found']);
    }

    /**
     * Handle Excel import preview.
     */
    public function import(Request $request, Courier $courier)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            // Quick implementation using basic Excel import or just parsing directly
            // For now, let's assume we use Maatwebsite Excel
            $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $request->file('excel_file'));
            
            if (empty($data) || empty($data[0])) {
                return response()->json(['success' => false, 'message' => 'Empty or invalid file']);
            }

            $rows = $data[0];
            $foundOrders = [];

            // Skip header row if exists (usually row 0)
            // Logic depends on Excel structure. Assuming Column A is Waybill.
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header

                $waybill = $row[0] ?? null; // Adjusted based on actual template
                if ($waybill) {
                    $order = $this->eligibleOrdersQuery($courier)
                        ->where('waybill_number', $waybill)
                        ->first();
                    
                    if ($order) {
                        $foundOrders[] = [
                            'waybill_number' => $order->waybill_number,
                            'order_no' => $order->id,
                            'customer_name' => $order->customer_name,
                            'destination' => $order->city->name ?? $order->customer_city,
                            'delivery_fee' => $order->delivery_fee,
                            'amount' => $order->total_amount,
                            'id' => $order->id
                        ];
                    }
                }
            }
            
            return response()->json(['success' => true, 'data' => $foundOrders]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Store the payment and update orders.
     */
    public function store(Request $request, Courier $courier)
    {
        $request->validate([
            'payment_account_id' => 'required|exists:bank_accounts,id',
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        try {
            DB::transaction(function() use ($request, $courier) {
                $selectedAccount = BankAccount::findOrFail($request->payment_account_id);
                $paymentDate = now()->toDateString();

                $eligibleOrders = $this->eligibleOrdersQuery($courier)
                    ->whereIn('id', $request->order_ids)
                    ->lockForUpdate()
                    ->get();

                if ($eligibleOrders->count() !== count($request->order_ids)) {
                    throw ValidationException::withMessages([
                        'order_ids' => 'One or more selected orders are invalid for this courier or already reconciled.',
                    ]);
                }

                // 1. Calculate total received amount from selected eligible orders.
                $totalAmount = (float) $eligibleOrders->sum('total_amount');

                // 2. Create Payment Record
                $payment = CourierPayment::create([
                    'courier_id' => $courier->id,
                    'user_id' => auth()->id(),
                    'amount' => $totalAmount,
                    'payment_date' => $paymentDate,
                    'payment_method' => $selectedAccount->display_label,
                    'bank_account_id' => $selectedAccount->id,
                    'reference_number' => null, // Or generated
                    'payment_note' => 'Received via Receive Courier Payment module',
                ]);

                // 3. Update Orders
                Order::whereIn('id', $eligibleOrders->pluck('id')->all())->update([
                    'courier_payment_id' => $payment->id,
                    'payment_status' => 'paid',
                ]);
            });

            return redirect()->route('courier-receive.index')->with('success', 'Payment received and orders updated successfully.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    private function eligibleOrdersQuery(Courier $courier)
    {
        return Order::query()
            ->where('courier_id', $courier->id)
            ->where('status', 'confirm')
            ->whereNull('courier_payment_id');
    }
}
