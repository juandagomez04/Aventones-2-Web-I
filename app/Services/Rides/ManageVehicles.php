<?php
declare(strict_types=1);

namespace App\Services\Rides;

use Config\Database;
use CodeIgniter\HTTP\Files\UploadedFile;

final class ManageVehicles
{
    public static function listByDriver(int $driverId): array
    {
        $db = Database::connect();

        return $db->table('vehicles')
            ->where('driver_id', $driverId)
            ->orderBy('year', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }

    public static function getByIdForDriver(int $vehicleId, int $driverId): ?array
    {
        $db = Database::connect();

        $row = $db->table('vehicles')
            ->where('id', $vehicleId)
            ->where('driver_id', $driverId)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    public static function create(int $driverId, array $data, ?UploadedFile $photo = null): int
    {
        $db = Database::connect();
        self::validate($driverId, $data, null);

        $photoPath = null;
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $photoPath = self::uploadPhoto($photo);
        }

        $insert = [
            'driver_id' => $driverId,
            'plate' => strtoupper(trim((string) $data['plate'])),
            'color' => trim((string) $data['color']),
            'make' => trim((string) $data['make']),
            'model' => trim((string) $data['model']),
            'year' => (int) $data['year'],
            'seats_capacity' => (int) $data['seats_capacity'],
            'photo_path' => $photoPath,
        ];

        $db->table('vehicles')->insert($insert);
        return (int) $db->insertID();
    }

    public static function update(int $vehicleId, int $driverId, array $data, ?UploadedFile $photo = null): bool
    {
        $db = Database::connect();

        $current = self::getByIdForDriver($vehicleId, $driverId);
        if (!$current) {
            throw new \RuntimeException('Vehículo no encontrado.');
        }

        self::validate($driverId, $data, $vehicleId);

        $photoPath = $current['photo_path'] ?? null;

        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            if ($photoPath) {
                $absOld = FCPATH . ltrim($photoPath, '/');
                if (is_file($absOld))
                    @unlink($absOld);
            }
            $photoPath = self::uploadPhoto($photo);
        }

        $update = [
            'plate' => strtoupper(trim((string) $data['plate'])),
            'color' => trim((string) $data['color']),
            'make' => trim((string) $data['make']),
            'model' => trim((string) $data['model']),
            'year' => (int) $data['year'],
            'seats_capacity' => (int) $data['seats_capacity'],
            'photo_path' => $photoPath,
        ];

        return (bool) $db->table('vehicles')
            ->where('id', $vehicleId)
            ->where('driver_id', $driverId)
            ->update($update);
    }

    public static function delete(int $vehicleId, int $driverId): bool
    {
        $db = Database::connect();

        $current = self::getByIdForDriver($vehicleId, $driverId);
        if (!$current)
            return false;

        $photoPath = $current['photo_path'] ?? null;
        if ($photoPath) {
            $abs = FCPATH . ltrim($photoPath, '/');
            if (is_file($abs))
                @unlink($abs);
        }

        return (bool) $db->table('vehicles')
            ->where('id', $vehicleId)
            ->where('driver_id', $driverId)
            ->delete();
    }

    public static function isOwner(int $vehicleId, int $driverId): bool
    {
        $db = Database::connect();

        return $db->table('vehicles')
            ->where('id', $vehicleId)
            ->where('driver_id', $driverId)
            ->countAllResults() > 0;
    }

    // =========================
    // Validación / helpers
    // =========================

    private static function validate(int $driverId, array $data, ?int $vehicleId): void
    {
        $required = ['plate', 'color', 'make', 'model', 'year', 'seats_capacity'];
        foreach ($required as $f) {
            if (!isset($data[$f]) || trim((string) $data[$f]) === '') {
                throw new \RuntimeException("El campo {$f} es requerido.");
            }
        }

        $year = (int) $data['year'];
        $currentYear = (int) date('Y');
        if ($year < 1990 || $year > $currentYear + 1) {
            throw new \RuntimeException("El año debe estar entre 1990 y " . ($currentYear + 1));
        }

        $cap = (int) $data['seats_capacity'];
        if ($cap < 1 || $cap > 20) {
            throw new \RuntimeException("La capacidad de asientos debe estar entre 1 y 20.");
        }

        self::validateUniquePlate($driverId, (string) $data['plate'], $vehicleId);
    }

    private static function validateUniquePlate(int $driverId, string $plate, ?int $vehicleId): void
    {
        $db = Database::connect();

        $b = $db->table('vehicles')
            ->select('id')
            ->where('driver_id', $driverId)
            ->where('plate', strtoupper(trim($plate)));

        if ($vehicleId) {
            $b->where('id !=', $vehicleId);
        }

        if ($b->countAllResults() > 0) {
            throw new \RuntimeException("Ya existe un vehículo con esta placa.");
        }
    }

    private static function uploadPhoto(UploadedFile $photo): string
    {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($photo->getMimeType(), $allowed, true)) {
            throw new \RuntimeException("Solo se permiten imágenes JPEG, PNG, GIF o WebP.");
        }

        if ($photo->getSize() > 5 * 1024 * 1024) {
            throw new \RuntimeException("La imagen no debe superar los 5MB.");
        }

        $newName = 'v_' . time() . '_' . random_int(1000, 9999) . '.' . $photo->getExtension();
        $photo->move(FCPATH . 'assets/img/vehicles', $newName);

        return 'assets/img/vehicles/' . $newName;
    }

    
}
