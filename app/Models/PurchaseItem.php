<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'stock_variant_id',
        'product_name',
        'quantity',
        'purchase_price',
        'total',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'stock_variant_id');
    }

    public function inventoryUnits()
    {
        return $this->hasMany(InventoryUnit::class);
    }

    public function trackedUnits(): Collection
    {
        $units = $this->relationLoaded('inventoryUnits')
            ? $this->inventoryUnits
            : $this->inventoryUnits()->orderBy('id')->get();

        return $units
            ->where('status', '!=', InventoryUnit::STATUS_ARCHIVED)
            ->sortBy('id')
            ->values();
    }

    public function trackedUnitCount(): int
    {
        return $this->trackedUnits()->count();
    }

    public function scannedUnitCount(): int
    {
        return $this->trackedUnits()
            ->whereIn('status', InventoryUnit::GRN_PROGRESS_STATUSES)
            ->count();
    }

    public function pendingUnitCount(): int
    {
        return max($this->trackedUnitCount() - $this->scannedUnitCount(), 0);
    }

    public function placedUnitCount(): int
    {
        return $this->trackedUnits()
            ->whereIn('status', InventoryUnit::ACTIVE_STOCK_STATUSES)
            ->count();
    }

    public function remainingPlacementQuantity(): int
    {
        return max((int) ($this->quantity ?? 0) - $this->placedUnitCount(), 0);
    }

    public function trackedUnitRangeLabel(): ?string
    {
        $units = $this->trackedUnits();
        $count = $units->count();

        if ($count === 0) {
            return null;
        }

        $firstCode = $units->first()?->barcode_value;
        $lastCode = $units->last()?->barcode_value;

        if (! $firstCode) {
            return null;
        }

        return $count === 1 || $firstCode === $lastCode
            ? $firstCode
            : $firstCode.' to '.$lastCode;
    }
}
