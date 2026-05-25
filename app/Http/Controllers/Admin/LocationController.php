<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = CompanyLocation::orderBy('name')->paginate(10);
        return view('admin.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->normalizeCoordinates($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'nullable|integer|min:1',
            'address' => 'nullable|string|max:500',
            'is_main' => 'nullable|boolean',
        ]);

        $validated['radius_meter'] = $validated['radius_meter'] ?? 100;
        $validated['status'] = true;
        $validated['is_main'] = $request->boolean('is_main') || !CompanyLocation::where('is_main', true)->exists();

        DB::transaction(function () use ($validated) {
            if ($validated['is_main']) {
                CompanyLocation::where('is_main', true)->update(['is_main' => false]);
            }

            CompanyLocation::create($validated);
        });

        return redirect()->route('admin.locations.index')->with('success', 'Vị trí GPS đã được thêm thành công');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompanyLocation $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompanyLocation $location)
    {
        $this->normalizeCoordinates($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'nullable|integer|min:1',
            'address' => 'nullable|string|max:500',
            'is_main' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        $validated['radius_meter'] = $validated['radius_meter'] ?? 100;
        $validated['status'] = $request->boolean('status');
        $validated['is_main'] = $request->boolean('is_main');

        DB::transaction(function () use ($location, $validated) {
            if ($validated['is_main']) {
                CompanyLocation::where('id', '!=', $location->id)
                    ->where('is_main', true)
                    ->update(['is_main' => false]);
            }

            $location->update($validated);
            $this->ensureMainLocation();
        });

        return redirect()->route('admin.locations.index')->with('success', 'Vị trí GPS đã được cập nhật thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompanyLocation $location)
    {
        $location->delete();
        $this->ensureMainLocation();

        return redirect()->route('admin.locations.index')->with('success', 'Vị trí GPS đã được xóa thành công');
    }

    private function ensureMainLocation(): void
    {
        if (CompanyLocation::where('is_main', true)->exists()) {
            return;
        }

        $fallback = CompanyLocation::where('status', true)
            ->orderBy('id')
            ->first();

        if ($fallback) {
            $fallback->update(['is_main' => true]);
        }
    }

    private function normalizeCoordinates(Request $request): void
    {
        $request->merge([
            'latitude' => $this->normalizeDecimal($request->input('latitude')),
            'longitude' => $this->normalizeDecimal($request->input('longitude')),
        ]);
    }

    private function normalizeDecimal($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return str_replace(',', '.', trim((string) $value));
    }
}
