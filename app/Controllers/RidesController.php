<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\Rides\ManageRides;
use App\Services\Bookings\ManageBookings;
use App\Services\Rides\ManageVehicles;

class RidesController extends BaseController
{
    private function requireLogin()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }
        return null;
    }

    private function requireDriver()
    {
        if ((string) session()->get('user_role') !== 'driver') {
            return redirect()->to('/rides/search')->with('msg', 'Solo para conductores.');
        }
        return null;
    }

    // =========================
    // DRIVER: My Rides
    // =========================
    public function my()
    {
        if ($r = $this->requireLogin())
            return $r;
        if ($r = $this->requireDriver())
            return $r;

        $driverId = (int) session()->get('user_id');

        $rides = ManageRides::listByDriver($driverId);

        return view('myrides/myrides', [
            'rides' => $rides,
            'role' => 'driver',
            'msg' => session()->getFlashdata('msg') ?? '',
        ]);
    }

    public function action()
    {
        if ($r = $this->requireLogin())
            return $r;
        if ($r = $this->requireDriver())
            return $r;

        $driverId = (int) session()->get('user_id');

        $rideId = (int) ($this->request->getPost('ride_id') ?? 0);
        $action = (string) ($this->request->getPost('action') ?? '');

        if ($rideId <= 0) {
            return redirect()->to('/rides/my')->with('msg', 'Ride inválido.');
        }

        if ($action === 'delete') {
            ManageRides::deleteRide($driverId, $rideId);
            return redirect()->to('/rides/my')->with('msg', 'Ride deleted');
        }

        return redirect()->to('/rides/my')->with('msg', 'Unknown action');
    }

    // =========================
    // DRIVER: Create Ride
    // =========================
    public function new()
    {
        if ($r = $this->requireLogin())
            return $r;
        if ($r = $this->requireDriver())
            return $r;

        $driverId = (int) session()->get('user_id');

        return view('myrides/newride', [
            'vehicles' => ManageVehicles::listByDriver($driverId),
            'errors' => session()->getFlashdata('errors') ?? [],
            'old' => session()->getFlashdata('old') ?? [],
            'msg' => session()->getFlashdata('msg') ?? '',
        ]);

    }

    public function store()
    {
        if ($r = $this->requireLogin())
            return $r;
        if ($r = $this->requireDriver())
            return $r;

        $driverId = (int) session()->get('user_id');
        $vehicleId = (int) ($this->request->getPost('vehicle_id') ?? 0);

        try {
            if ($vehicleId <= 0 || !ManageVehicles::isOwner($vehicleId, $driverId)) {
                throw new \RuntimeException('Ese vehículo no te pertenece.');
            }

            $data = [
                'vehicle_id' => $vehicleId,
                'name' => (string) $this->request->getPost('name'),
                'origin' => (string) $this->request->getPost('origin'),
                'destination' => (string) $this->request->getPost('destination'),
                'days' => $this->request->getPost('days') ?? [],
                'time' => (string) $this->request->getPost('time'),
                'seat_price' => $this->request->getPost('seat_price'),
                'seats_total' => $this->request->getPost('seats_total'),
            ];

            ManageRides::createRide($driverId, $data);
            return redirect()->to('/rides/my')->with('msg', 'Ride created');

        } catch (\Throwable $e) {
            return redirect()->to('/rides/new')
                ->with('msg', $e->getMessage())
                ->with('errors', ['general' => $e->getMessage()])
                ->with('old', $this->request->getPost());
        }
    }

    // =========================
    // DRIVER: Edit Ride
    // =========================
    public function edit(int $rideId)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $role = (string) session()->get('user_role');
        if ($role !== 'driver') {
            return redirect()->to('/rides/search');
        }

        $driverId = (int) session()->get('user_id');

        $ride = ManageRides::getRideForDriver($driverId, $rideId);
        if (!$ride) {
            return redirect()->to('/rides/my')->with('msg', 'Ride not found');
        }

        $vehicles = ManageVehicles::listByDriver($driverId);

        return view('myrides/editride', [
            'ride' => $ride,
            'vehicles' => $vehicles,
            'errors' => session()->getFlashdata('errors') ?? [],
            'msg' => session()->getFlashdata('msg') ?? '',
            'role' => $role,
        ]);
    }



    public function update(int $rideId)
    {
        if ($r = $this->requireLogin())
            return $r;
        if ($r = $this->requireDriver())
            return $r;

        $driverId = (int) session()->get('user_id');
        $vehicleId = (int) ($this->request->getPost('vehicle_id') ?? 0);

        try {
            if ($vehicleId <= 0 || !ManageVehicles::isOwner($vehicleId, $driverId)) {
                throw new \RuntimeException('Ese vehículo no te pertenece.');
            }

            $data = [
                'vehicle_id' => $vehicleId,
                'name' => (string) $this->request->getPost('name'),
                'origin' => (string) $this->request->getPost('origin'),
                'destination' => (string) $this->request->getPost('destination'),
                'days' => $this->request->getPost('days') ?? [],
                'time' => (string) $this->request->getPost('time'),
                'seat_price' => $this->request->getPost('seat_price'),
                'seats_total' => $this->request->getPost('seats_total'),
            ];

            ManageRides::updateRide($driverId, $rideId, $data);
            return redirect()->to('/rides/my')->with('msg', 'Ride updated');

        } catch (\Throwable $e) {
            return redirect()->to('/rides/edit/' . $rideId)
                ->with('msg', $e->getMessage())
                ->with('errors', ['general' => $e->getMessage()]);
        }
    }

    // =========================
    // PUBLIC: Details
    // =========================
    public function details(int $rideId)
    {
        $ride = ManageRides::getRideDetails($rideId); // ✅ este método debe existir
        if (!$ride) {
            return redirect()->to('/rides/search')->with('msg', 'Ride not found');
        }

        $photo = (string) ($ride['vehicle_photo'] ?? '');   // viene de vehicles.photo_path
        $photo = preg_replace('#^public/#', '', $photo);  // quita "public/" si existe
        $photo = ltrim($photo, '/');

        $ride['vehicle_photo_url'] = $photo !== '' ? base_url($photo) : '';
        $ride['available_seats'] = ManageBookings::getAvailableSeats((int) $ride['id']);

        return view('rides/detailsride', [
            'ride' => $ride,
            'isLoggedIn' => (bool) session()->get('user_id'),
            'role' => (string) (session()->get('user_role') ?? ''),
        ]);
    }

    // =========================
    // PUBLIC: Search
    // =========================
    public function search()
    {
        $isLoggedIn = (bool) session()->get('user_id');
        $role = (string) (session()->get('user_role') ?? '');
        $userId = (int) (session()->get('user_id') ?? 0);

        $origin = trim((string) ($this->request->getGet('origin') ?? ''));
        $destination = trim((string) ($this->request->getGet('destination') ?? ''));
        $sort = (string) ($this->request->getGet('sort') ?? 'time');
        $dir = strtolower((string) ($this->request->getGet('dir') ?? 'asc')) === 'desc' ? 'desc' : 'asc';

        try {
            $origins = ManageRides::getDistinctOrigins();
            $map = ManageRides::getOriginDestinationMap();
            $destForSelected = $origin !== '' ? ManageRides::getDestinationsByOrigin($origin) : [];
            $mapJson = json_encode($map, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        } catch (\Throwable $e) {
            $origins = [];
            $destForSelected = [];
            $mapJson = '{}';
        }

        try {
            $rides = ManageRides::search($origin ?: null, $destination ?: null, null);

            // enrich
            foreach ($rides as &$r) {
                $rideId = (int) ($r['id'] ?? 0);

                $r['available_seats'] = $rideId ? (int) ManageBookings::getAvailableSeats($rideId) : 0;
                $r['already_booked'] = ($role === 'passenger' && $userId > 0)
                    ? (bool) ManageBookings::hasActiveBooking($rideId, $userId)
                    : false;
            }
            unset($r);

            // ✅ LOG: guardar búsqueda solo si realmente buscaron algo
            $didSearch = ($origin !== '' || $destination !== '');
            if ($didSearch) {
                $db = \Config\Database::connect();
                $db->table('search_logs')->insert([
                    'user_id' => $userId > 0 ? $userId : null,
                    'origin' => $origin,
                    'destination' => $destination,
                    'results_count' => is_array($rides) ? count($rides) : 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

        } catch (\Throwable $e) {
            $rides = [];
        }

        return view('rides/searchrides', compact(
            'isLoggedIn',
            'role',
            'userId',
            'origin',
            'destination',
            'sort',
            'dir',
            'origins',
            'destForSelected',
            'rides',
            'mapJson'
        ));
    }

}
