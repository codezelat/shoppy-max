<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Reseller;
use App\Models\ResellerPayment;
use Illuminate\Support\Collection;

class ResellerDueService
{
    public function sync(?int $resellerId): void
    {
        if (! $resellerId) {
            return;
        }

        $reseller = Reseller::query()->lockForUpdate()->find($resellerId);
        if (! $reseller) {
            return;
        }

        $ordersQuery = Order::query()
            ->where('order_type', 'reseller')
            ->where('reseller_id', $reseller->id)
            ->where('status', '!=', 'cancel');

        $orderTotal = round((float) (clone $ordersQuery)->sum('total_amount'), 2);
        $returnFeeDeduction = 0.0;

        if ($reseller->reseller_type === Reseller::TYPE_RESELLER) {
            $returnFeeDeduction = round((float) (clone $ordersQuery)
                ->where('return_fee_reseller_id', $reseller->id)
                ->sum('reseller_return_fee_applied'), 2);
        }

        $paidTotal = round((float) ResellerPayment::query()
            ->where('reseller_id', $reseller->id)
            ->where('status', 'paid')
            ->sum('amount'), 2);

        $reseller->due_amount = round($orderTotal - $returnFeeDeduction - $paidTotal, 2);
        $reseller->save();
    }

    public function syncMany(iterable $resellerIds): void
    {
        Collection::make($resellerIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->each(fn (int $id) => $this->sync($id));
    }
}
