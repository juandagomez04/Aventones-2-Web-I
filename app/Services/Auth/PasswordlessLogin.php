<?php

namespace App\Services\Auth;

use Config\Database;
use App\Services\Mail\MailSender;

class PasswordlessLogin
{
    public static function sendLoginLink(string $email): array
    {
        $db = Database::connect();

        // Buscar usuario por email
        $user = $db->table('users')
            ->select('id, status')
            ->where('email', $email)
            ->get()
            ->getRowArray();

        if (!$user) {
            // por seguridad puedes devolver success igual,
            return ['success' => false, 'message' => 'No account found with that email.'];
        }

        if (($user['status'] ?? '') !== 'active') {
            return ['success' => false, 'message' => 'Your account is not active yet.'];
        }

        $token = bin2hex(random_bytes(32)); // 64 chars
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $ok = $db->table('passwordless_tokens')->insert([
            'user_id' => (int) $user['id'],
            'token' => $token,
            'expires_at' => $expiresAt,
            'used_at' => null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if (!$ok) {
            return ['success' => false, 'message' => 'Could not generate login link.'];
        }

        $sent = MailSender::sendPasswordlessLoginMail($email, $token);

        return ['success' => true, 'mail_sent' => $sent];
    }

    public static function consumeTokenAndLogin(string $token): array
    {
        $db = Database::connect();

        $row = $db->table('passwordless_tokens')
            ->select('user_id, expires_at, used_at')
            ->where('token', $token)
            ->get()
            ->getRowArray();

        if (!$row) {
            return ['success' => false, 'message' => 'Invalid link.'];
        }

        if (!empty($row['used_at'])) {
            return ['success' => false, 'message' => 'This link was already used.'];
        }

        if (strtotime($row['expires_at']) < time()) {
            return ['success' => false, 'message' => 'This link has expired.'];
        }

        // Marcar token como usado
        $db->table('passwordless_tokens')
            ->where('token', $token)
            ->update(['used_at' => date('Y-m-d H:i:s')]);

        // Traer usuario
        $user = $db->table('users')
            ->select('id, role, status')
            ->where('id', $row['user_id'])
            ->get()
            ->getRowArray();

        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if (($user['status'] ?? '') !== 'active') {
            return ['success' => false, 'message' => 'Account is not active.'];
        }

        return [
            'success' => true,
            'user_id' => $user['id'],
            'role' => $user['role'],
            'status' => $user['status'],
        ];
    }
}
