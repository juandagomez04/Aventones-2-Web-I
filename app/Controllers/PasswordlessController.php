<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\Auth\PasswordlessLogin;

class PasswordlessController extends BaseController
{
    public function sendLink()
    {
        $email = trim((string) $this->request->getPost('pwless_email'));

        if ($email === '') {
            return redirect()->back()->with('error', 'Please enter your email.');
        }

        $result = PasswordlessLogin::sendLoginLink($email);

        if (!($result['success'] ?? false)) {
            return redirect()->back()->with('error', $result['message'] ?? 'Could not send login link.');
        }

        return redirect()->back()->with(
            'success',
            'Token created. Mail sent: ' . (($result['mail_sent'] ?? false) ? 'YES' : 'NO')
        );
    }

    public function login(string $token)
    {
        $result = PasswordlessLogin::consumeTokenAndLogin($token);

        if (!($result['success'] ?? false)) {
            return redirect()->to('/login')->with('error', $result['message'] ?? 'Invalid or expired link.');
        }

        // crear sesión
        session()->set([
            'user_id' => $result['user_id'],
            'user_role' => $result['role'],
            'user_status' => $result['status'],
        ]);

        // redirección según rol
        return match ($result['role']) {
            'admin' => redirect()->to('/admin'),
            'driver' => redirect()->to('/rides/my'),
            'passenger' => redirect()->to('/rides/search'),
            default => redirect()->to('/login'),
        };
    }
}
