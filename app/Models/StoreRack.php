<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreRack extends Model
{
    public const STORE_RETAIL = 'retail';

    public const STORE_WAREHOUSE = 'warehouse';

    public const STORE_LABELS = [
        self::STORE_RETAIL => 'Retail Store',
        self::STORE_WAREHOUSE => 'Warehouse Store',
    ];

    protected $fillable = [
        'store_type',
        'rack_name',
        'rack_key',
        'row_name',
        'row_key',
    ];

    protected $appends = [
        'display_label',
    ];

    public static function normalizeStoreType(string $storeType): string
    {
        return strtolower(trim($storeType));
    }

    public static function isValidStoreType(string $storeType): bool
    {
        return array_key_exists(self::normalizeStoreType($storeType), self::STORE_LABELS);
    }

    public static function storeLabel(string $storeType): string
    {
        return self::STORE_LABELS[self::normalizeStoreType($storeType)] ?? 'Store';
    }

    public static function normalizeRowKey(string $rowName): string
    {
        return mb_strtolower(preg_replace('/\s+/', ' ', trim($rowName)));
    }

    public static function normalizeRackKey(string $rackName): string
    {
        return mb_strtolower(preg_replace('/\s+/', ' ', trim($rackName)));
    }

    public function getDisplayLabelAttribute(): string
    {
        $rackName = trim((string) ($this->rack_name ?: 'Default Rack'));
        $rowName = trim((string) $this->row_name);

        return $rowName !== '' ? "{$rackName} / {$rowName}" : $rackName;
    }

    public function inventoryUnits(): HasMany
    {
        return $this->hasMany(InventoryUnit::class);
    }
}
