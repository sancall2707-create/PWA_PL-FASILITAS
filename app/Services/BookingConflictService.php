<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class BookingConflictService
{
    /**
     * Cek konflik booking berdasarkan tipe bookable
     *
     * @param int $bookableId ID dari Vehicle atau Facility
     * @param string $bookableType Tipe: App\Models\Vehicle atau App\Models\Facility
     * @param string $dateFrom Tanggal mulai (Y-m-d format)
     * @param string $dateTo Tanggal selesai (Y-m-d format)
     * @param string|null $timeFrom Jam mulai (H:i format) - untuk vehicle
     * @param string|null $timeTo Jam selesai (H:i format) - untuk vehicle
     * @param int|null $excludeBookingId ID booking yang dikecualikan (untuk update case)
     * @return array ['hasConflict' => bool, 'conflicts' => [...], 'message' => string]
     */
    public function checkConflict(
        int $bookableId,
        string $bookableType,
        string $dateFrom,
        string $dateTo,
        ?string $timeFrom = null,
        ?string $timeTo = null,
        ?int $excludeBookingId = null
    ): array {
        // Validasi input
        $dateFromObj = Carbon::createFromFormat('Y-m-d', $dateFrom);
        $dateToObj = Carbon::createFromFormat('Y-m-d', $dateTo);

        if (!$dateFromObj || !$dateToObj) {
            return [
                'hasConflict' => false,
                'conflicts' => [],
                'message' => 'Format tanggal tidak valid',
            ];
        }

        // Cek apakah FASILITAS (Ruangan/Lapangan)
        $isFacility = strpos($bookableType, 'Facility') !== false;

        if ($isFacility) {
            return $this->checkFacilityConflict($bookableId, $dateFrom, $dateTo, $excludeBookingId);
        } else {
            // KENDARAAN
            return $this->checkVehicleConflict($bookableId, $dateFrom, $dateTo, $timeFrom, $timeTo, $excludeBookingId);
        }
    }

    /**
     * Cek konflik untuk FASILITAS (full date basis)
     * Aturan: Jika sudah ada yang APPROVED pada tanggal apapun dalam rentang, maka bentrok
     */
    private function checkFacilityConflict(
        int $facilityId,
        string $dateFrom,
        string $dateTo,
        ?int $excludeBookingId = null
    ): array {
        $query = Booking::where('bookable_type', 'App\\Models\\Facility')
            ->where('bookable_id', $facilityId)
            ->where('status', 'approved'); // Hanya cek yang sudah approved

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        // Cek apakah ada yang beririsan dengan tanggal yang diminta
        $conflicts = $query
            ->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date_from', [$dateFrom, $dateTo])
                    ->orWhereBetween('date_to', [$dateFrom, $dateTo])
                    ->orWhere(function ($subQ) use ($dateFrom, $dateTo) {
                        $subQ->where('date_from', '<=', $dateFrom)
                            ->where('date_to', '>=', $dateTo);
                    });
            })
            ->get();

        if ($conflicts->isNotEmpty()) {
            return [
                'hasConflict' => true,
                'conflicts' => $conflicts->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'date_from' => $booking->date_from,
                        'date_to' => $booking->date_to,
                        'responsible_person' => $booking->responsible_person,
                    ];
                })->toArray(),
                'message' => sprintf(
                    'Fasilitas ini sudah disetujui pada tanggal %s s/d %s. Tidak dapat menyetujui peminjaman pada periode yang sama.',
                    $conflicts->first()->date_from,
                    $conflicts->first()->date_to
                ),
            ];
        }

        return [
            'hasConflict' => false,
            'conflicts' => [],
            'message' => 'Tidak ada konflik - fasilitas tersedia',
        ];
    }

    /**
     * Cek konflik untuk KENDARAAN (date + time overlapping basis)
     * Aturan: Bentrok jika ada yang APPROVED dengan tanggal beririsan DAN waktu beririsan
     */
    private function checkVehicleConflict(
        int $vehicleId,
        string $dateFrom,
        string $dateTo,
        ?string $timeFrom,
        ?string $timeTo,
        ?int $excludeBookingId = null
    ): array {
        $query = Booking::where('bookable_type', 'App\\Models\\Vehicle')
            ->where('bookable_id', $vehicleId)
            ->where('status', 'approved');

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        // Cek date overlapping + time overlapping
        $conflicts = $query
            ->where(function ($q) use ($dateFrom, $dateTo) {
                // Date overlapping check
                $q->whereBetween('date_from', [$dateFrom, $dateTo])
                    ->orWhereBetween('date_to', [$dateFrom, $dateTo])
                    ->orWhere(function ($subQ) use ($dateFrom, $dateTo) {
                        $subQ->where('date_from', '<=', $dateFrom)
                            ->where('date_to', '>=', $dateTo);
                    });
            })
            ->get()
            ->filter(function ($booking) use ($timeFrom, $timeTo) {
                // Tambahan: Time overlapping check untuk hasil date overlapping
                return $this->isTimeOverlapping(
                    $timeFrom,
                    $timeTo,
                    $booking->time_from,
                    $booking->time_to
                );
            });

        if ($conflicts->isNotEmpty()) {
            return [
                'hasConflict' => true,
                'conflicts' => $conflicts->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'date_from' => $booking->date_from,
                        'date_to' => $booking->date_to,
                        'time_from' => $booking->time_from,
                        'time_to' => $booking->time_to,
                        'responsible_person' => $booking->responsible_person,
                    ];
                })->toArray(),
                'message' => sprintf(
                    'Kendaraan ini sudah disetujui pada %s (jam %s-%s). Tidak dapat menyetujui pada periode/waktu yang bersamaan.',
                    $conflicts->first()->date_from,
                    $conflicts->first()->time_from,
                    $conflicts->first()->time_to
                ),
            ];
        }

        return [
            'hasConflict' => false,
            'conflicts' => [],
            'message' => 'Tidak ada konflik - kendaraan tersedia',
        ];
    }

    /**
     * Cek apakah dua rentang waktu beririsan
     * @param string $start1 Format H:i (misal: "08:00")
     * @param string $end1 Format H:i (misal: "17:00")
     * @param string $start2 Format H:i
     * @param string $end2 Format H:i
     */
    private function isTimeOverlapping(string $start1, string $end1, string $start2, string $end2): bool
    {
        $start1Min = $this->timeToMinutes($start1);
        $end1Min = $this->timeToMinutes($end1);
        $start2Min = $this->timeToMinutes($start2);
        $end2Min = $this->timeToMinutes($end2);

        // Overlap terjadi jika: start1 < end2 AND start2 < end1
        return $start1Min < $end2Min && $start2Min < $end1Min;
    }

    /**
     * Convert time string (H:i) ke menit
     */
    private function timeToMinutes(string $time): int
    {
        [$hours, $minutes] = explode(':', $time);
        return intval($hours) * 60 + intval($minutes);
    }
}
