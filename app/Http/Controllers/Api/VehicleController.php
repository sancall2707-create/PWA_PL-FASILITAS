<?php

namespace App\Http\Controllers\Api;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VehicleController
{
    public function index()
    {
        return response()->json([
            'vehicles' => Vehicle::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Vehicle::class);

        $validated = $request->validate([
            'name' => 'required|string',
            'plate_number' => 'nullable|string',
            'series_number' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $vehicle = Vehicle::create($validated);

        return response()->json([
            'message' => 'Kendaraan berhasil ditambahkan',
            'vehicle' => $vehicle,
        ], 201);
    }

    public function show(Vehicle $vehicle)
    {
        return response()->json([
            'vehicle' => $vehicle,
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        Gate::authorize('update', $vehicle);

        $validated = $request->validate([
            'name' => 'string',
            'plate_number' => 'nullable|string',
            'series_number' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $vehicle->update($validated);

        return response()->json([
            'message' => 'Kendaraan berhasil diperbarui',
            'vehicle' => $vehicle,
        ]);
    }

    public function destroy(Vehicle $vehicle)
    {
        Gate::authorize('delete', $vehicle);

        $vehicle->delete();

        return response()->json([
            'message' => 'Kendaraan berhasil dihapus',
        ]);
    }
}