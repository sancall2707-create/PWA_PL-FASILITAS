<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        // Semua user yang login bisa list bookings (controller akan filter)
        return true;
    }

    public function view(User $user, Booking $booking): bool
    {
        // User hanya bisa lihat booking miliknya, admin bisa lihat semua
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

    public function create(User $user): bool
    {
        // User yang login bisa buat booking
        return true;
    }

    public function update(User $user, Booking $booking): bool
    {
        // User hanya bisa update booking miliknya (jika masih pending), admin bisa update semua
        return $user->isAdmin() || ($user->id === $booking->user_id && $booking->status === 'pending');
    }

    public function updateStatus(User $user, Booking $booking): bool
    {
        // Hanya admin yang bisa approve/reject status
        return $user->isAdmin();
    }

    public function delete(User $user, Booking $booking): bool
    {
        // User bisa delete booking miliknya, admin bisa delete semua
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

    public function restore(User $user, Booking $booking): bool
    {
        return false;
    }

    public function forceDelete(User $user, Booking $booking): bool
    {
        return false;
    }
}
