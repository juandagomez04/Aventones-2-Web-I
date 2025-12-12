<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\Bookings\ManageBookings;

class BookingsController extends BaseController
{
    public function index()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $userId = (int) session()->get('user_id');
        $role = (string) (session()->get('user_role') ?? '');

        // OJO: este método debe traer al menos:
        // booking_id (o id), ride_id, status, origin, destination, days, time, seats_total,
        // ride_name (o name), vehicle (o make/model/year)
        $rows = ManageBookings::getBookingsForUser($userId, $role);

        $bookings = [];

        foreach ($rows as $r) {
            $rideId = (int) ($r['ride_id'] ?? 0);
            $bookingId = (int) ($r['booking_id'] ?? ($r['id'] ?? 0));

            $status = strtolower((string) ($r['status'] ?? ''));
            $statusLabel = ucfirst($status);
            $statusClass = match ($status) {
                'pending' => 'badge-warning',
                'accepted' => 'badge-success',
                'rejected' => 'badge-danger',
                'cancelled' => 'badge-muted',
                default => 'badge-secondary',
            };

            // asientos disponibles
            $availableSeats = $rideId ? (int) ManageBookings::getAvailableSeats($rideId) : 0;

            // acciones (la vista las renderiza)
            $actions = [];

            if ($role === 'driver' && $status === 'pending') {
                $actions[] = [
                    'label' => 'Aceptar',
                    'class' => 'btn btn-sm btn-success',
                    'post_action' => 'accept',
                    'confirm' => 'Aceptar esta reserva?',
                    'fields' => ['booking_id' => $bookingId],
                ];
                $actions[] = [
                    'label' => 'Rechazar',
                    'class' => 'btn btn-sm btn-danger',
                    'post_action' => 'reject',
                    'confirm' => 'Rechazar esta reserva?',
                    'fields' => ['booking_id' => $bookingId],
                ];
            }

            if ($role === 'passenger' && in_array($status, ['pending', 'accepted'], true)) {
                $actions[] = [
                    'label' => 'Cancelar',
                    'class' => 'btn btn-sm btn-warning',
                    'post_action' => 'cancel',
                    'confirm' => 'Cancelar esta reserva?',
                    'fields' => ['booking_id' => $bookingId],
                ];
            }

            // Normalizar campos que la vista espera
            $rideName = $r['ride_name'] ?? $r['name'] ?? ('#' . $rideId);

            // Si tu query no trae "vehicle" ya listo, lo armamos
            $vehicle = $r['vehicle']
                ?? trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? '') . ' ' . ($r['year'] ?? ''));

            $bookings[] = [
                'booking_id' => $bookingId,
                'ride_id' => $rideId,

                'ride_name' => (string) $rideName,
                'origin' => (string) ($r['origin'] ?? ''),
                'destination' => (string) ($r['destination'] ?? ''),
                'days' => (string) ($r['days'] ?? ''),
                'time' => substr((string) ($r['time'] ?? ''), 0, 5),
                'vehicle' => (string) $vehicle,
                'seats_total' => (int) ($r['seats_total'] ?? 0),

                'available_seats' => $availableSeats,
                'status_label' => $statusLabel,
                'status_class' => $statusClass,
                'actions' => $actions,
            ];
        }

        return view('bookings/bookings', [
            'isLoggedIn' => true,
            'role' => $role,
            'bookings' => $bookings,
            'msg' => session()->getFlashdata('msg') ?? '',
        ]);
    }

    public function action()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $role = (string) (session()->get('user_role') ?? '');
        $userId = (int) session()->get('user_id');

        $action = (string) ($this->request->getPost('action') ?? '');
        $bookingId = (int) ($this->request->getPost('booking_id') ?? 0);
        $rideId = (int) ($this->request->getPost('ride_id') ?? 0);

        try {
            if ($action === 'create' && $role === 'passenger') {
                ManageBookings::createBooking($rideId, $userId);
                return redirect()->to('/bookings')->with('msg', 'Reserva creada (PENDING)');
            }

            if ($action === 'accept' && $role === 'driver') {
                ManageBookings::updateBookingStatus($bookingId, 'accepted', $userId, $role);
                return redirect()->to('/bookings')->with('msg', 'Reserva aceptada');
            }

            if ($action === 'reject' && $role === 'driver') {
                ManageBookings::updateBookingStatus($bookingId, 'rejected', $userId, $role);
                return redirect()->to('/bookings')->with('msg', 'Reserva rechazada');
            }

            if ($action === 'cancel' && $role === 'passenger') {
                ManageBookings::updateBookingStatus($bookingId, 'cancelled', $userId, $role);
                return redirect()->to('/bookings')->with('msg', 'Reserva cancelada');
            }

            return redirect()->to('/bookings')->with('msg', 'Acción no permitida');
        } catch (\Throwable $e) {
            return redirect()->to('/bookings')->with('msg', $e->getMessage());
        }
    }
}
