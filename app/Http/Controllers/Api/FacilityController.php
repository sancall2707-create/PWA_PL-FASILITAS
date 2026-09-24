<?php

namespace App\Http\Controllers\Api;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FacilityController
{
    public function index()
    {
        return response()->json([
            'facilities' => Facility::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Facility::class);

        $validated = $request->validate([
            'name' => 'required|string',
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $facility = Facility::create($validated);

        return response()->json([
            'message' => 'Fasilitas berhasil ditambahkan',
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
        Gate::authorize('update', $facility);

        $validated = $request->validate([
            'name' => 'string',
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $facility->update($validated);

        return response()->json([
            'message' => 'Fasilitas berhasil diperbarui',
            'facility' => $facility,
        ]);
    }

    public function destroy(Facility $facility)
    {
        Gate::authorize('delete', $facility);

        $facility->delete();

        return response()->json([
            'message' => 'Fasilitas berhasil dihapus',
        ]);
    }
}
