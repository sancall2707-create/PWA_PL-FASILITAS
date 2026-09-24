<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingController
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Admin lihat semua booking, User biasa hanya lihat booking miliknya sendiri
        $query = Booking::with(['user', 'category', 'bookable']);

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return response()->json([
            'bookings' => $query->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Booking::class);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'bookable_type' => 'required|in:App\\Models\\Vehicle,App\\Models\\Facility',
            'bookable_id' => 'required|integer',
            'driver' => 'nullable|string',
            'purpose' => 'nullable|string',
            'organizer' => 'nullable|string',
            'event_name' => 'nullable|string',
            'event_description' => 'nullable|string',
            'responsible_person' => 'required|string',
            'responsible_phone' => 'required|string',
            'requested_facilities_qty' => 'nullable|integer',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'time_from' => 'required|date_format:H:i',
            'time_to' => 'required|date_format:H:i',
            'confirmation_status' => 'in:sudah,belum',
            'contact_info' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $booking = Booking::create($validated);

        return response()->json([
            'message' => 'Peminjaman berhasil dibuat',
            'booking' => $booking->load(['user', 'category', 'bookable']),
        ], 201);
    }

    public function show(Booking $booking)
    {
        Gate::authorize('view', $booking);

        return response()->json([
            'booking' => $booking->load(['user', 'category', 'bookable']),
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        Gate::authorize('update', $booking);

        $validated = $request->validate([
            'driver' => 'nullable|string',
            'purpose' => 'nullable|string',
            'organizer' => 'nullable|string',
            'event_name' => 'nullable|string',
            'event_description' => 'nullable|string',
            'responsible_person' => 'string',
            'responsible_phone' => 'string',
            'requested_facilities_qty' => 'nullable|integer',
            'date_from' => 'date',
            'date_to' => 'date',
            'time_from' => 'date_format:H:i',
            'time_to' => 'date_format:H:i',
            'confirmation_status' => 'in:sudah,belum',
            'contact_info' => 'nullable|string',
        ]);

        $booking->update($validated);

        return response()->json([
            'message' => 'Peminjaman berhasil diperbarui',
            'booking' => $booking->load(['user', 'category', 'bookable']),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        Gate::authorize('updateStatus', $booking);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
        ]);

        $booking->update($validated);

        return response()->json([
            'message' => 'Status peminjaman berhasil diperbarui',
            'booking' => $booking->load(['user', 'category', 'bookable']),
        ]);
    }

    public function destroy(Booking $booking)
    {
        Gate::authorize('delete', $booking);

        $booking->delete();

        return response()->json([
            'message' => 'Peminjaman berhasil dihapus',
        ]);
    }
}
