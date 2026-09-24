<?php

namespace App\Http\Controllers\Api;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController
{
    public function index()
    {
        return response()->json([
            'facilities' => Facility::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $facility = Facility::create($validated);

        return response()->json([
            'message' => 'Facility created',
            'facility' => $facility,
        ], 201);
    }

    public function show(Facility $facility)
    {
        return response()->json([
            'facility' => $facility,
        ]);
    }

    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name' => 'string',
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $facility->update($validated);

        return response()->json([
            'message' => 'Facility updated',
            'facility' => $facility,
        ]);
    }

    public function destroy(Facility $facility)
    {
        $facility->delete();

        return response()->json([
            'message' => 'Facility deleted',
        ]);
    }
}
