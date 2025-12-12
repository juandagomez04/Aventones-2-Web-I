<?php

namespace App\Services\Admin;

use Config\Database;
use CodeIgniter\HTTP\IncomingRequest;

class AdminActions
{
    public static function getUsers(string $roleFilter, string $statusFilter): array
    {
        $db = Database::connect();
        $builder = $db->table('users');

        if ($roleFilter !== 'all') {
            $builder->where('role', $roleFilter);
        }
        if ($statusFilter !== 'all') {
            $builder->where('status', $statusFilter);
        }

        return $builder->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public static function handle(?string $action, IncomingRequest $request): array
    {
        $db = Database::connect();
        $users = $db->table('users');

        if ($action === 'update_status') {
            $id = (int) $request->getPost('id');
            $status = $request->getPost('status');

            $users->where('id', $id)->update(['status' => $status]);

            return ['success' => true, 'message' => 'Status updated'];
        }

        if ($action === 'delete') {
            $id = (int) $request->getPost('id');

            $loggedId = (int) session()->get('user_id');
            if ($id === $loggedId) {
                return ['success' => false, 'message' => 'You cannot delete your own account'];
            }

            $users->where('id', $id)->delete();

            return ['success' => true, 'message' => 'User deleted'];
        }

        if ($action === 'create') {

            $first = trim((string) $request->getPost('first_name'));
            $last = trim((string) $request->getPost('last_name'));
            $email = trim((string) $request->getPost('email'));
            $pass1 = (string) $request->getPost('password');
            $pass2 = (string) $request->getPost('password2');

            $nationalId = trim((string) $request->getPost('national_id'));
            $birthDate = $request->getPost('birth_date') ?: null;
            $phone = trim((string) $request->getPost('phone'));

            if ($pass1 !== $pass2) {
                return ['success' => false, 'message' => 'Passwords do not match'];
            }

            if (strlen($pass1) < 6) {
                return ['success' => false, 'message' => 'Password must be at least 6 characters'];
            }

            // Email único
            $exists = $users->where('email', $email)->countAllResults();
            if ($exists > 0) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // (Opcional) subir foto - si todavía no lo manejarás, déjalo null
            $photoPath = null;

            $insertData = [
                'role' => 'admin',
                'status' => 'active',
                'first_name' => $first,
                'last_name' => $last,
                'email' => $email,
                'national_id' => $nationalId ?: null,
                'birth_date' => $birthDate,
                'phone' => $phone ?: null,
                'photo_path' => $photoPath,
                'password_hash' => password_hash($pass1, PASSWORD_DEFAULT),
            ];

            if (!$users->insert($insertData)) {
                $err = $db->error();
                return ['success' => false, 'message' => $err['message'] ?? 'Database error creating admin'];
            }

            return ['success' => true, 'message' => 'Admin created'];
        }

        return ['success' => false, 'message' => 'Unknown action'];
    }
}
