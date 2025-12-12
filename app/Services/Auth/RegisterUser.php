<?php

namespace App\Services\Auth;

use Config\Database;
use App\Services\Mail\MailSender;
use CodeIgniter\I18n\Time;

class RegisterUser
{
    /**
     * Registrar PASAJERO
     */
    public static function registerPassenger(array $data): array
    {
        $db = Database::connect();
        $builder = $db->table('users');

        $insertData = [
            'role' => 'passenger',
            'status' => 'pending',
            'first_name' => $data['fname'] ?? '',
            'last_name' => $data['lname'] ?? '',
            'national_id' => $data['cedula'] ?? '',
            'birth_date' => $data['dob'] ?? null,
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'photo_path' => null,
            'password_hash' => password_hash($data['password'] ?? '', PASSWORD_DEFAULT),
        ];

        if (!$builder->insert($insertData)) {
            $error = $db->error();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Database error while registering passenger',
            ];
        }

        // ID del usuario creado
        $userId = $db->insertID();

        // generar token
        $token = bin2hex(random_bytes(32));

        // guardar token en tabla activation_tokens
        $okToken = $db->table('activation_tokens')->insert([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours')),
            'used_at' => null,
        ]);

        if (!$okToken) {
            // opcional: si falla token, marcamos error
            return [
                'success' => false,
                'message' => 'User created but activation token could not be generated.',
            ];
        }

        // enviar correo (si falla, no bloquea el registro)
        $sent = MailSender::sendActivationMail($insertData['email'], $token);

        return [
            'success' => true,
            'mail_sent' => $sent,
        ];
    }


    /**
     * Registrar CONDUCTOR
     */
    public static function registerDriver(array $data): array
    {
        $db = Database::connect();
        $builder = $db->table('users');

        $insertData = [
            'role' => 'driver',
            'status' => 'pending',
            'first_name' => $data['fname'] ?? '',
            'last_name' => $data['lname'] ?? '',
            'national_id' => $data['cedula'] ?? '',
            'birth_date' => $data['dob'] ?? null,
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'photo_path' => null,
            'password_hash' => password_hash($data['password'] ?? '', PASSWORD_DEFAULT),
        ];

        if (!$builder->insert($insertData)) {
            $error = $db->error();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Database error while registering driver',
            ];
        }

        $userId = $db->insertID();
        $token = bin2hex(random_bytes(32));

        $okToken = $db->table('activation_tokens')->insert([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours')),
            'used_at' => null,
        ]);

        if (!$okToken) {
            return [
                'success' => false,
                'message' => 'User created but activation token could not be generated.',
            ];
        }

        $sent = MailSender::sendActivationMail($insertData['email'], $token);

        return [
            'success' => true,
            'mail_sent' => $sent,
        ];
    }


    public static function activateAccount(string $token): array
    {
        $db = Database::connect();

        $row = $db->table('activation_tokens')
            ->select('user_id, expires_at, used_at')
            ->where('token', $token)
            ->get()
            ->getRowArray();

        if (!$row) {
            return ['success' => false, 'message' => 'Invalid token.'];
        }

        if (!empty($row['used_at'])) {
            return ['success' => false, 'message' => 'This activation link was already used.'];
        }

        if (!empty($row['expires_at']) && strtotime($row['expires_at']) < time()) {
            return ['success' => false, 'message' => 'This activation link has expired.'];
        }

        // activar usuario
        $db->table('users')
            ->where('id', $row['user_id'])
            ->update(['status' => 'active']);

        // marcar token como usado
        $db->table('activation_tokens')
            ->where('token', $token)
            ->update(['used_at' => date('Y-m-d H:i:s')]);

        return ['success' => true, 'message' => 'Account activated.'];
    }


}
