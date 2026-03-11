<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SchoolDayRequest;
use App\Models\SchoolDay;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolDayController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'in:class,holiday,event'],
            'month' => ['nullable', 'date_format:Y-m'],
            'is_school_open' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ]);

        $schoolDays = SchoolDay::query()
            ->when($validated['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($schoolDayQuery) use ($search): void {
                    $schoolDayQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($validated['type'] ?? null, function ($query, string $type): void {
                $query->where('type', $type);
            })
            ->when($validated['month'] ?? null, function ($query, string $month): void {
                $monthDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $query
                    ->whereYear('date', $monthDate->year)
                    ->whereMonth('date', $monthDate->month);
            })
            ->when(array_key_exists('is_school_open', $validated), function ($query) use ($validated): void {
                $query->where('is_school_open', filter_var($validated['is_school_open'], FILTER_VALIDATE_BOOLEAN));
            })
            ->orderByDesc('date')
            ->paginate($validated['per_page'] ?? 15)
            ->withQueryString();

        return response()->json($schoolDays);
    }

    public function store(SchoolDayRequest $request): JsonResponse
    {
        $schoolDay = SchoolDay::query()->create($this->normalizePayload($request->validated()));

        return response()->json([
            'message' => 'School day created successfully.',
            'school_day' => $schoolDay,
        ], 201);
    }

    public function show(SchoolDay $schoolDay): JsonResponse
    {
        return response()->json([
            'school_day' => $schoolDay,
        ]);
    }

    public function update(SchoolDayRequest $request, SchoolDay $schoolDay): JsonResponse
    {
        $schoolDay->update($this->normalizePayload($request->validated(), $schoolDay));

        return response()->json([
            'message' => 'School day updated successfully.',
            'school_day' => $schoolDay->fresh(),
        ]);
    }

    public function destroy(SchoolDay $schoolDay): JsonResponse
    {
        $schoolDay->delete();

        return response()->json([
            'message' => 'School day deleted successfully.',
        ]);
    }

    /**
     * Keep holiday entries internally consistent even when the client submits extra fields.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizePayload(array $data, ?SchoolDay $schoolDay = null): array
    {
        $type = $data['type'] ?? $schoolDay?->type;

        if ($type === 'holiday') {
            $data['attendance_rate'] = null;
            $data['is_school_open'] = false;
        }

        return $data;
    }
}
