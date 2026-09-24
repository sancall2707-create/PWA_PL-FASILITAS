<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController
{
    public function index()
    {
        $bookings = Booking::with(['user', 'category', 'bookable'])->get();

        return response()->json([
            'bookings' => $bookings,
        ]);
    }

    public function store(Request $request)
    {
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
            'message' => 'Booking created',
            'booking' => $booking->load(['user', 'category', 'bookable']),
        ], 201);
    }

    public function show(Booking $booking)
    {
        return response()->json([
            'booking' => $booking->load(['user', 'category', 'bookable']),
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        // Only allow owner or admin to update
        if ($request->user()->id !== $booking->user_id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

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
            'message' => 'Booking updated',
            'booking' => $booking->load(['user', 'category', 'bookable']),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        // Only admin can update status
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
        ]);

        $booking->update($validated);

        return response()->json([
            'message' => 'Booking status updated',
            'booking' => $booking->load(['user', 'category', 'bookable']),
        ]);
    }

    public function destroy(Request $request, Booking $booking)
    {
        // Only allow owner or admin to delete
        if ($request->user()->id !== $booking->user_id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $booking->delete();

        return response()->json([
            'message' => 'Booking deleted',
        ]);
    }
}
