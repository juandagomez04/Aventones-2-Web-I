<?php

namespace App\Services\Auth;

use Config\Database;

class LoginUser
{
    public static function authenticate(string $email, string $password): array
    {
        $db = Database::connect();

        $user = $db->table('users')
            ->where('email', $email)
            ->get()
            ->getRowArray();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        // Cuenta pendiente
        if ($user['status'] !== 'active') {
            return [
                'success' => false,
                'message' => 'Account not active',
                'status'  => $user['status']
            ];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return [
                'success' => false,
                'message' => 'Invalid password'
            ];
        }

        return [
            'success' => true,
            'user_id' => $user['id'],
            'role'    => $user['role'], 
        ];
    }

    public static function isAccountPending(string $email): bool
    {
        $db = Database::connect();

        return $db->table('users')
            ->where('email', $email)
            ->where('status', 'pending')
            ->countAllResults() > 0;
    }
}
