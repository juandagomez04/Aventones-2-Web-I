<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\Rides\ManageVehicles;

class VehiclesController extends BaseController
{
    private function requireLogin()
    {
        if (!session()->get('user_id'))
            return redirect()->to('/login');
        return null;
    }

    private function requireDriver()
    {
        if ((string) session()->get('user_role') !== 'driver')
            return redirect()->to('/rides/search');
        return null;
    }

    public function index()
    {
        if ($r = $this->requireLogin())
            return $r;
        if ($r = $this->requireDriver())
            return $r;

        $driverId = (int) session()->get('user_id');

        return view('myrides/managevehicles', [
            'vehicles' => ManageVehicles::listByDriver($driverId),
            'editingVehicle' => null,
            'deleteConfirm' => null,
            'msg' => session()->getFlashdata('msg') ?? '',
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function edit(int $id)
    {
        if ($r = $this->requireLogin())
            return $r;
        if ($r = $this->requireDriver())
            return $r;

        $driverId = (int) session()->get('user_id');

        $vehicle = ManageVehicles::getByIdForDriver($id, $driverId);
        if (!$vehicle) {
            return redirect()->to('/vehicles')->with('msg', 'Vehículo no encontrado.');
        }

        return view('myrides/managevehicles', [
            'vehicles' => ManageVehicles::listByDriver($driverId),
            'editingVehicle' => $vehicle,
            'deleteConfirm' => null,
            'msg' => session()->getFlashdata('msg') ?? '',
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function store()
    {
        if ($r = $this->requireLogin())
            return $r;
        if ($r = $this->requireDriver())
            return $r;

        $driverId = (int) session()->get('user_id');

        try {
            $data = [
                'plate' => $this->request->getPost('plate'),
                'color' => $this->request->getPost('color'),
                'make' => $this->request->getPost('make'),
                'model' => $this->request->getPost('model'),
                'year' => $this->request->getPost('year'),
                'seats_capacity' => $this->request->getPost('seats_capacity'),
            ];

            $photo = $this->request->getFile('photo');
            ManageVehicles::create($driverId, $data, ($photo && $photo->isValid()) ? $photo : null);

            return redirect()->to('/vehicles')->with('msg', '✅ Vehículo creado exitosamente.');
        } catch (\Throwable $e) {
            return redirect()->to('/vehicles')
                ->with('errors', ['general' => $e->getMessage()])
                ->with('msg', $e->getMessage());
        }
    }

    public function update(int $id)
    {
        if ($r = $this->requireLogin())
            return $r;
        if ($r = $this->requireDriver())
            return $r;

        $driverId = (int) session()->get('user_id');

        try {
            $data = [
                'plate' => $this->request->getPost('plate'),
                'color' => $this->request->getPost('color'),
                'make' => $this->request->getPost('make'),
                'model' => $this->request->getPost('model'),
                'year' => $this->request->getPost('year'),
                'seats_capacity' => $this->request->getPost('seats_capacity'),
            ];

            $photo = $this->request->getFile('photo');
            ManageVehicles::update($id, $driverId, $data, ($photo && $photo->isValid()) ? $photo : null);

            return redirect()->to('/vehicles')->with('msg', '✅ Vehículo actualizado exitosamente.');
        } catch (\Throwable $e) {
            return redirect()->to('/vehicles')
                ->with('errors', ['general' => $e->getMessage()])
                ->with('msg', $e->getMessage());
        }
    }

    public function confirmDelete(int $id)
    {
        if ($r = $this->requireLogin())
            return $r;
        if ($r = $this->requireDriver())
            return $r;

        $driverId = (int) session()->get('user_id');

        $vehicle = ManageVehicles::getByIdForDriver($id, $driverId);
        if (!$vehicle) {
            return redirect()->to('/vehicles')->with('msg', 'Vehículo no encontrado.');
        }

        return view('myrides/managevehicles', [
            'vehicles' => ManageVehicles::listByDriver($driverId),
            'editingVehicle' => null,
            'deleteConfirm' => ['id' => (int) $vehicle['id'], 'plate' => (string) $vehicle['plate']],
            'msg' => session()->getFlashdata('msg') ?? '',
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function delete(int $id)
    {
        if ($r = $this->requireLogin())
            return $r;
        if ($r = $this->requireDriver())
            return $r;

        $driverId = (int) session()->get('user_id');

        ManageVehicles::delete($id, $driverId);
        return redirect()->to('/vehicles')->with('msg', '✅ Vehículo eliminado exitosamente.');
    }
}
