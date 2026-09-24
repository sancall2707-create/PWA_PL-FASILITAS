<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // Publik bisa lihat daftar kendaraan aktif
    }

    public function view(?User $user, Vehicle $vehicle): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin(); // Hanya admin
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->isAdmin(); // Hanya admin
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->isAdmin(); // Hanya admin
    }
}
