<?php

namespace App\Http\Controllers\Api;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController
{
    public function index()
    {
        return response()->json([
            'vehicles' => Vehicle::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'plate_number' => 'nullable|string',
            'series_number' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $vehicle = Vehicle::create($validated);

        return response()->json([
            'message' => 'Vehicle created',
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
        $validated = $request->validate([
            'name' => 'string',
            'plate_number' => 'nullable|string',
            'series_number' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $vehicle->update($validated);

        return response()->json([
            'message' => 'Vehicle updated',
            'vehicle' => $vehicle,
        ]);
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return response()->json([
            'message' => 'Vehicle deleted',
        ]);
    }
}
