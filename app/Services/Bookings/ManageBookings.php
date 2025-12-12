<?php
declare(strict_types=1);

namespace App\Services\Bookings;

use Config\Database;

final class ManageBookings
{
    public static function getBookingsForUser(int $userId, string $role): array
    {
        return ($role === 'driver')
            ? self::getBookingsByDriver($userId)
            : self::getBookingsByPassenger($userId);
    }

    public static function getBookingsByPassenger(int $passengerId): array
    {
        $db = Database::connect();

        return $db->table('bookings b')
            ->select("
                b.id AS booking_id,
                b.ride_id,
                b.status,
                r.name AS ride_name,
                r.origin,
                r.destination,
                r.days,
                r.time,
                r.seats_total,
                CONCAT(COALESCE(v.make,''),' ',COALESCE(v.model,''),' ',COALESCE(v.year,'')) AS vehicle
            ")
            ->join('rides r', 'r.id = b.ride_id')
            ->join('vehicles v', 'v.id = r.vehicle_id', 'left')
            ->where('b.passenger_id', $passengerId)
            ->orderBy('b.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    public static function getBookingsByDriver(int $driverId): array
    {
        $db = Database::connect();

        return $db->table('bookings b')
            ->select("
                b.id AS booking_id,
                b.ride_id,
                b.status,
                r.name AS ride_name,
                r.origin,
                r.destination,
                r.days,
                r.time,
                r.seats_total,
                CONCAT(COALESCE(v.make,''),' ',COALESCE(v.model,''),' ',COALESCE(v.year,'')) AS vehicle
            ")
            ->join('rides r', 'r.id = b.ride_id')
            ->join('users u', 'u.id = b.passenger_id')
            ->join('vehicles v', 'v.id = r.vehicle_id', 'left')
            ->where('r.driver_id', $driverId)
            ->orderBy('b.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    public static function getAvailableSeats(int $rideId): int
    {
        $db = Database::connect();

        $total = (int) ($db->table('rides')->select('seats_total')->where('id', $rideId)->get()->getRow('seats_total') ?? 0);

        $accepted = (int) ($db->table('bookings')
            ->select('COUNT(*) AS c')
            ->where('ride_id', $rideId)
            ->where('status', 'accepted')
            ->get()
            ->getRow('c') ?? 0);

        return max(0, $total - $accepted);
    }

    public static function hasActiveBooking(int $rideId, int $passengerId): bool
    {
        $db = Database::connect();

        $c = (int) ($db->table('bookings')
            ->select('COUNT(*) AS c')
            ->where('ride_id', $rideId)
            ->where('passenger_id', $passengerId)
            ->whereIn('status', ['pending', 'accepted'])
            ->get()
            ->getRow('c') ?? 0);

        return $c > 0;
    }

    public static function createBooking(int $rideId, int $passengerId): void
    {
        $db = Database::connect();

        // evita duplicados
        if (self::hasActiveBooking($rideId, $passengerId)) {
            throw new \RuntimeException('Ya tienes una reserva activa en este viaje.');
        }

        if (self::getAvailableSeats($rideId) <= 0) {
            throw new \RuntimeException('No hay asientos disponibles.');
        }

        $db->table('bookings')->insert([
            'ride_id' => $rideId,
            'passenger_id' => $passengerId,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function updateBookingStatus(
        int $bookingId,
        string $status,
        int $userId,
        string $role
    ): bool {
        $db = Database::connect();
        $status = strtolower($status);

        // Estados válidos
        if (!in_array($status, ['accepted', 'rejected', 'cancelled'], true)) {
            throw new \RuntimeException('Estado no válido');
        }

        // Contexto
        $booking = self::getBookingContext($bookingId);
        if (!$booking) {
            throw new \RuntimeException('Reserva no encontrada');
        }

        $currentStatus = strtolower($booking['status']);
        $rideId = (int) $booking['ride_id'];
        $driverId = (int) $booking['driver_id'];
        $passengerId = (int) $booking['passenger_id'];

        // No hay cambio real
        if ($currentStatus === $status) {
            return true;
        }

        /* ======================
        REGLAS POR ROL
        ====================== */

        if ($role === 'driver') {

            if ($driverId !== $userId) {
                throw new \RuntimeException('No autorizado');
            }

            if ($currentStatus !== 'pending') {
                throw new \RuntimeException('Solo se pueden gestionar reservas PENDING');
            }

            if (!in_array($status, ['accepted', 'rejected'], true)) {
                throw new \RuntimeException('Acción no permitida');
            }

            // Aceptar → validar cupo
            if ($status === 'accepted') {
                if (self::getAvailableSeats($rideId) <= 0) {
                    throw new \RuntimeException('No hay asientos disponibles');
                }
            }

        } elseif ($role === 'passenger') {

            if ($passengerId !== $userId) {
                throw new \RuntimeException('No autorizado');
            }

            if ($status !== 'cancelled') {
                throw new \RuntimeException('Solo puedes cancelar reservas');
            }

            if (!in_array($currentStatus, ['pending', 'accepted'], true)) {
                throw new \RuntimeException('No se puede cancelar este estado');
            }

        } else {
            throw new \RuntimeException('Rol no permitido');
        }

        // Actualización final
        $updated = $db->table('bookings')
            ->where('id', $bookingId)
            ->update(['status' => $status]);

        return (bool) $updated;
    }


    private static function getBookingContext(int $bookingId): ?array
    {
        $db = Database::connect();

        return $db->table('bookings b')
            ->select('
            b.id,
            b.status,
            b.ride_id,
            b.passenger_id,
            r.driver_id
        ')
            ->join('rides r', 'r.id = b.ride_id')
            ->where('b.id', $bookingId)
            ->get()
            ->getRowArray() ?: null;
    }



}
