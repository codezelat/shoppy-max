<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\CourierWaybill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourierWaybillController extends Controller
{
    private const MAX_RANGE_SIZE = 5000;
    private const PER_PAGE = 15;

    public function index(Request $request, Courier $courier): JsonResponse
    {
        $waybills = CourierWaybill::query()
            ->with('order:id,order_number')
            ->where('courier_id', $courier->id)
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return response()->json([
            'courier' => [
                'id' => $courier->id,
                'name' => $courier->name,
            ],
            'summary' => $this->buildSummary($courier),
            'items' => $waybills->getCollection()->map(function (CourierWaybill $waybill) {
                return [
                    'id' => $waybill->id,
                    'code' => $waybill->code,
                    'status' => $waybill->order_id ? 'allocated' : 'available',
                    'order_number' => $waybill->order?->order_number,
                    'allocated_at' => optional($waybill->allocated_at)->format('Y-m-d H:i:s'),
                ];
            })->values()->all(),
            'pagination' => [
                'current_page' => $waybills->currentPage(),
                'last_page' => $waybills->lastPage(),
                'per_page' => $waybills->perPage(),
                'total' => $waybills->total(),
            ],
        ]);
    }

    public function store(Request $request, Courier $courier): JsonResponse
    {
        $validated = $request->validate([
            'prefix' => 'nullable|string|max:50',
            'start_number' => 'required|integer|min:0',
            'end_number' => 'required|integer|gt:start_number',
            'suffix' => 'nullable|string|max:50',
        ]);

        $prefix = trim((string) ($validated['prefix'] ?? ''));
        $suffix = trim((string) ($validated['suffix'] ?? ''));
        $startNumber = (int) $validated['start_number'];
        $endNumber = (int) $validated['end_number'];
        $rangeSize = ($endNumber - $startNumber) + 1;

        if ($rangeSize > self::MAX_RANGE_SIZE) {
            throw ValidationException::withMessages([
                'end_number' => 'Waybill range is too large. Add up to ' . self::MAX_RANGE_SIZE . ' IDs at a time.',
            ]);
        }

        $codes = collect(range($startNumber, $endNumber))
            ->map(fn (int $number) => $prefix . $number . $suffix)
            ->values();

        $duplicates = CourierWaybill::query()
            ->whereIn('code', $codes->all())
            ->orderBy('code')
            ->limit(5)
            ->pluck('code');

        if ($duplicates->isNotEmpty()) {
            throw ValidationException::withMessages([
                'start_number' => 'Some waybill IDs already exist: ' . $duplicates->implode(', ') . '.',
            ]);
        }

        DB::transaction(function () use ($courier, $codes, $prefix, $suffix, $startNumber, $endNumber): void {
            $timestamp = now();

            $rows = $codes->map(function (string $code, int $index) use ($courier, $prefix, $suffix, $startNumber, $endNumber, $timestamp) {
                $number = $startNumber + $index;

                return [
                    'courier_id' => $courier->id,
                    'code' => $code,
                    'prefix' => $prefix !== '' ? $prefix : null,
                    'sequence_number' => $number,
                    'suffix' => $suffix !== '' ? $suffix : null,
                    'range_start' => $startNumber,
                    'range_end' => $endNumber,
                    'order_id' => null,
                    'allocated_at' => null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            })->all();

            foreach (array_chunk($rows, 500) as $chunk) {
                CourierWaybill::query()->insert($chunk);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Waybill IDs added successfully.',
            'summary' => $this->buildSummary($courier->fresh()),
        ]);
    }

    private function buildSummary(Courier $courier): array
    {
        $availableQuery = $courier->waybills()->available();

        return [
            'total_waybills' => $courier->waybills()->count(),
            'available_waybills' => (clone $availableQuery)->count(),
            'allocated_waybills' => $courier->waybills()->whereNotNull('order_id')->count(),
            'next_available_waybill' => (clone $availableQuery)->orderBy('id')->value('code'),
        ];
    }
}
