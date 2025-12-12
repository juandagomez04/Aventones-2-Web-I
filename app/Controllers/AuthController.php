<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\Auth\LoginUser;
use App\Services\Auth\RegisterUser;

class AuthController extends BaseController
{
    /**
     * LOGIN
     */
    public function login()
    {
        $session = session();
        $error_message = '';
        $info_message = '';

        if ($session->get('user_id') && $session->get('user_status') === 'active') {
            return $this->redirectByRole($session->get('user_role'));
        }

        if ($this->request->getMethod() === 'POST') {

            $email = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $result = LoginUser::authenticate($email, $password);

            if ($result['success']) {

                $session->set([
                    'user_id' => $result['user_id'],
                    'user_role' => $result['role'],
                    'user_status' => 'active',
                ]);

                return $this->redirectByRole($result['role']);
            }

            $error_message = $result['message'];

            if (LoginUser::isAccountPending($email)) {
                $info_message = "Your account is awaiting approval.";
            }
        }

        return view('auth/login', compact('error_message', 'info_message'));
    }



    // Redirige al usuario según su rol
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin' => redirect()->to('/admin'),
            'driver' => redirect()->to('/rides/my'),
            'passenger' => redirect()->to('/rides/search'),
            default => redirect()->to('/login'),
        };
    }

    /**
     * REGISTRO PASSENGER (GET + POST en la misma URL)
     */
    public function registerPassenger()
    {
        $error_message = '';

        // si envió el formulario
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            $result = RegisterUser::registerPassenger($data);

            if ($result['success']) {
                return redirect()->to('/login');
            }

            $error_message = $result['message'] ?? 'Error registering passenger';
        }

        // GET o error → mostrar vista
        return view('auth/register_passenger', [
            'error_message' => $error_message,
        ]);
    }

    /**
     * REGISTRO DRIVER (similar)
     */
    public function registerDriver()
    {
        $error_message = '';

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            $result = RegisterUser::registerDriver($data);

            if ($result['success']) {
                return redirect()->to('/login');
            }

            $error_message = $result['message'] ?? 'Error registering driver';
        }

        return view('auth/register_drivers', [
            'error_message' => $error_message,
        ]);
    }

    public function activate($token)
    {
        $result = RegisterUser::activateAccount($token);

        return view('auth/activate', [
            'success' => $result['success'] ?? false,
            'message' => $result['message'] ?? '',
        ]);
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

}
