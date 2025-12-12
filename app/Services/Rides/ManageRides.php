<?php
declare(strict_types=1);

namespace App\Services\Rides;

use Config\Database;

final class ManageRides
{
    // =========================
    // CRUD - RIDES (Driver)
    // =========================

    public static function listByDriver(int $driverId): array
    {
        $db = Database::connect();

        return $db->table('rides r')
            ->select('r.*, v.plate, v.make, v.model, v.year')
            ->join('vehicles v', 'v.id = r.vehicle_id', 'left')
            ->where('r.driver_id', $driverId)
            ->orderBy('r.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    public static function createRide(int $driverId, array $data): int
    {
        $db = Database::connect();

        $vehicleId = (int) ($data['vehicle_id'] ?? 0);
        $name = trim((string) ($data['name'] ?? ''));
        $origin = trim((string) ($data['origin'] ?? ''));
        $destination = trim((string) ($data['destination'] ?? ''));
        $time = trim((string) ($data['time'] ?? ''));
        $seatPrice = (float) ($data['seat_price'] ?? 0);
        $seatsTotal = (int) ($data['seats_total'] ?? 0);

        $daysArr = $data['days'] ?? [];
        if (!is_array($daysArr))
            $daysArr = [];
        $daysArr = array_values(array_filter(array_map('trim', $daysArr)));
        $daysStr = implode(',', $daysArr);

        self::validateRide($vehicleId, $name, $origin, $destination, $time, $seatPrice, $seatsTotal, $daysArr);

        $db->table('rides')->insert([
            'driver_id' => $driverId,
            'vehicle_id' => $vehicleId,
            'name' => $name,
            'origin' => $origin,
            'destination' => $destination,
            'days' => $daysStr,
            'time' => $time,
            'seat_price' => $seatPrice,
            'seats_total' => $seatsTotal,
        ]);

        return (int) $db->insertID();
    }

    public static function getRideForDriver(int $driverId, int $rideId): ?array
    {
        $db = Database::connect();

        $row = $db->table('rides')
            ->where('id', $rideId)
            ->where('driver_id', $driverId)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    public static function updateRide(int $driverId, int $rideId, array $data): bool
    {
        $db = Database::connect();

        $ride = self::getRideForDriver($driverId, $rideId);
        if (!$ride) {
            throw new \RuntimeException('Ride not found or not authorized.');
        }

        $vehicleId = (int) ($data['vehicle_id'] ?? 0);
        $name = trim((string) ($data['name'] ?? ''));
        $origin = trim((string) ($data['origin'] ?? ''));
        $destination = trim((string) ($data['destination'] ?? ''));
        $time = trim((string) ($data['time'] ?? ''));
        $seatPrice = (float) ($data['seat_price'] ?? 0);
        $seatsTotal = (int) ($data['seats_total'] ?? 0);

        $daysArr = $data['days'] ?? [];
        if (!is_array($daysArr))
            $daysArr = [];
        $daysArr = array_values(array_filter(array_map('trim', $daysArr)));
        $daysStr = implode(',', $daysArr);

        self::validateRide($vehicleId, $name, $origin, $destination, $time, $seatPrice, $seatsTotal, $daysArr);

        return (bool) $db->table('rides')
            ->where('id', $rideId)
            ->where('driver_id', $driverId)
            ->update([
                'vehicle_id' => $vehicleId,
                'name' => $name,
                'origin' => $origin,
                'destination' => $destination,
                'days' => $daysStr,
                'time' => $time,
                'seat_price' => $seatPrice,
                'seats_total' => $seatsTotal,
            ]);
    }

    public static function deleteRide(int $driverId, int $rideId): bool
    {
        $db = Database::connect();

        return (bool) $db->table('rides')
            ->where('id', $rideId)
            ->where('driver_id', $driverId)
            ->delete();
    }

    // =========================
    // PUBLIC - DETAILS / SEARCH
    // =========================

    public static function getRideDetails(int $rideId): ?array
    {
        $db = Database::connect();

        $row = $db->table('rides r')
            ->select('r.*, v.photo_path AS vehicle_photo, v.plate AS vehicle_plate, v.make AS vehicle_make, v.model AS vehicle_model, v.year AS vehicle_year, u.first_name, u.last_name')
            ->join('vehicles v', 'v.id = r.vehicle_id', 'left')
            ->join('users u', 'u.id = r.driver_id', 'left')
            ->where('r.id', $rideId)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    public static function search(?string $origin, ?string $destination, ?string $day): array
    {
        $db = Database::connect();
        $b = $db->table('rides r');

        $b->select('r.*, v.make, v.model, v.year')
            ->join('vehicles v', 'v.id = r.vehicle_id', 'left');

        if ($origin) {
            $b->like('r.origin', $origin);
        }
        if ($destination) {
            $b->like('r.destination', $destination);
        }
        if ($day) {
            $b->like('r.days', $day);
        }

        return $b->orderBy('r.id', 'DESC')
            ->limit(200)
            ->get()
            ->getResultArray();
    }

    public static function getDistinctOrigins(): array
    {
        $db = Database::connect();

        $rows = $db->table('rides')
            ->select('origin')
            ->where('origin IS NOT NULL', null, false)
            ->where('origin !=', '')
            ->groupBy('origin')
            ->orderBy('origin', 'ASC')
            ->get()
            ->getResultArray();

        return array_map(static fn($r) => (string) $r['origin'], $rows);
    }

    public static function getDestinationsByOrigin(string $origin): array
    {
        $db = Database::connect();

        $rows = $db->table('rides')
            ->select('destination')
            ->where('origin', $origin)
            ->where('destination IS NOT NULL', null, false)
            ->where('destination !=', '')
            ->groupBy('destination')
            ->orderBy('destination', 'ASC')
            ->get()
            ->getResultArray();

        return array_map(static fn($r) => (string) $r['destination'], $rows);
    }

    public static function getOriginDestinationMap(): array
    {
        $db = Database::connect();

        $rows = $db->table('rides')
            ->select('origin, destination')
            ->where('origin IS NOT NULL', null, false)
            ->where('destination IS NOT NULL', null, false)
            ->where('origin !=', '')
            ->where('destination !=', '')
            ->groupBy(['origin', 'destination'])
            ->orderBy('origin', 'ASC')
            ->orderBy('destination', 'ASC')
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $o = (string) $row['origin'];
            $d = (string) $row['destination'];
            $map[$o] ??= [];
            $map[$o][] = $d;
        }
        return $map;
    }

    // =========================
    // VALIDATION
    // =========================
    private static function validateRide(
        int $vehicleId,
        string $name,
        string $origin,
        string $destination,
        string $time,
        float $seatPrice,
        int $seatsTotal,
        array $daysArr
    ): void {
        if ($vehicleId <= 0)
            throw new \RuntimeException('Vehicle is required.');
        if ($name === '')
            throw new \RuntimeException('Name is required.');
        if ($origin === '')
            throw new \RuntimeException('Origin is required.');
        if ($destination === '')
            throw new \RuntimeException('Destination is required.');
        if ($time === '')
            throw new \RuntimeException('Time is required.');
        if ($seatsTotal <= 0)
            throw new \RuntimeException('Seats total must be > 0.');
        if ($seatPrice < 0)
            throw new \RuntimeException('Seat price must be >= 0.');
        if (empty($daysArr))
            throw new \RuntimeException('Select at least one day.');
    }
}
