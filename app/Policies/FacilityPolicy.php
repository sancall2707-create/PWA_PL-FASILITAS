<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Facility;

class FacilityPolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // Publik bisa lihat daftar fasilitas aktif
    }

    public function view(?User $user, Facility $facility): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin(); // Hanya admin
    }

    public function update(User $user, Facility $facility): bool
    {
        return $user->isAdmin(); // Hanya admin
    }

    public function delete(User $user, Facility $facility): bool
    {
        return $user->isAdmin(); // Hanya admin
    }
}
