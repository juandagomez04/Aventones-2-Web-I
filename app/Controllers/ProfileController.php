<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\Auth\ManageProfile;

class ProfileController extends BaseController
{
    private function requireLogin()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }
        return null;
    }

    public function index()
{
    // Verificar si el usuario está logueado
    if (!session()->has('user_id')) {
        return redirect()->to('login')->with('error', 'Debes iniciar sesión');
    }
    
    // Obtener el ID del usuario desde la sesión
    $userId = session()->get('user_id');
    
    // Cargar el modelo correcto: ServiceManageProfile
    $profileModel = new ManageProfile();
    // O si está en otro namespace, ajústalo:
    // $profileModel = new \App\Models\ServiceManageProfile();
    
    // Buscar el perfil del usuario por ID
    $userProfile = $profileModel->getUserProfile($userId);
    
    // Verificar si el perfil existe
    if (!$userProfile) {
        return redirect()->to('login')->with('error', 'Perfil no encontrado');
    }
    
    // Preparar datos para la vista
    $data = [
        'user' => $userProfile, // Cambié a userProfile para ser más específico
        'title' => 'Mi Perfil'
    ];
    
    // Cargar la vista
    return view('profile/editprofile', $data);
}
    /**
     * Mostrar formulario de edición de perfil
     */
    public function edit()
    {
        if ($r = $this->requireLogin()) {
            return $r;
        }

        $userId = (int) session()->get('user_id');

        try {
            $userData = ManageProfile::getUserProfile($userId);
            $birthForInput = ManageProfile::normalizeDateForInput($userData['birth_date'] ?? '');

            return view('profile/edit', [
                'user' => $userData,
                'birth_date' => $birthForInput,
                'msg' => session()->getFlashdata('msg') ?? '',
                'errors' => session()->getFlashdata('errors') ?? [],
                'old' => session()->getFlashdata('old') ?? [],
            ]);

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('msg', 'Error al cargar el perfil: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar perfil
     */
    public function update()
    {
        if ($r = $this->requireLogin()) {
            return $r;
        }

        $userId = (int) session()->get('user_id');

        try {
            $updateData = [
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'national_id' => $this->request->getPost('national_id'),
                'birth_date' => $this->request->getPost('birth_date'),
                'email' => $this->request->getPost('email'),
                'phone' => $this->request->getPost('phone'),
            ];

            $photoFile = $this->request->getFile('photo');
            $photoArray = null;

            if ($photoFile && $photoFile->isValid() && !$photoFile->hasMoved()) {
                $photoArray = [
                    'name' => $photoFile->getName(),
                    'type' => $photoFile->getMimeType(),
                    'tmp_name' => $photoFile->getTempName(),
                    'error' => $photoFile->getError(),
                    'size' => $photoFile->getSize(),
                ];
            }

            $success = ManageProfile::updateUserProfile($userId, $updateData, $photoArray);

            if (!$success) {
                throw new \RuntimeException('No se pudo actualizar el perfil.');
            }

            return redirect()->to('/profile/edit')
                ->with('msg', '✅ Perfil actualizado correctamente.');

        } catch (\Throwable $e) {
            return redirect()->to('/profile/edit')
                ->with('msg', '❌ ' . $e->getMessage())
                ->with('errors', ['general' => $e->getMessage()])
                ->with('old', $this->request->getPost());
        }
    }

    /**
     * Actualizar contraseña
     */
    public function updatePassword()
    {
        if ($r = $this->requireLogin()) {
            return $r;
        }

        $userId = (int) session()->get('user_id');
        $current = $this->request->getPost('current_password');
        $new = $this->request->getPost('new_password');
        $confirm = $this->request->getPost('confirm_password');

        try {
            if (empty($new) || empty($confirm)) {
                throw new \RuntimeException('La nueva contraseña no puede estar vacía.');
            }

            if ($new !== $confirm) {
                throw new \RuntimeException('La confirmación no coincide.');
            }

            if (strlen($new) < 8) {
                throw new \RuntimeException('La nueva contraseña debe tener al menos 8 caracteres.');
            }

            ManageProfile::updatePassword($userId, $current, $new);

            return redirect()->to('/profile/edit')
                ->with('msg', '✅ Contraseña actualizada correctamente.');

        } catch (\Throwable $e) {
            return redirect()->to('/profile/edit')
                ->with('msg', '❌ ' . $e->getMessage());
        }
    }
}