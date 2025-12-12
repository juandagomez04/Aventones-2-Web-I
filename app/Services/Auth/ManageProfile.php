<?php
declare(strict_types=1);

namespace App\Services\Auth;

use Config\Database;

final class ManageProfile
{
    private const DEFAULT_AVATAR = 'assets/img/avatar.png';
    private const USERS_DIR = 'assets/img/users';

    

    /** Perfil del usuario */
    public static function getUserProfile(int $userId): array
    {
        $db = Database::connect();

        $row = $db->table('users')
            ->select('id, first_name, last_name, email, phone, photo_path, national_id, birth_date, role, status')
            ->where('id', $userId)
            ->get()
            ->getRowArray();

        if (!$row) {
            return [];
        }

        // Normaliza ruta de foto
        if (empty($row['photo_path'])) {
            $row['photo_path'] = self::DEFAULT_AVATAR;
        } else {
            // Normalizar path para MVC (remover 'public/' si existe)
            $row['photo_path'] = self::normalizePhotoPath($row['photo_path']);
        }

        // Normaliza fechas 'cero'
        if (
            !empty($row['birth_date']) &&
            ($row['birth_date'] === '0000-00-00' || $row['birth_date'] === '0000-00-00 00:00:00')
        ) {
            $row['birth_date'] = '';
        }

        return $row;
    }

    /**
     * Normaliza path de foto para CodeIgniter
     */
    private static function normalizePhotoPath(string $path): string
    {
        // Remueve 'public/' del inicio si existe
        if (strpos($path, 'public/') === 0) {
            $path = substr($path, 7);
        }

        // Asegurar que comience con 'assets/'
        if (strpos($path, 'assets/') !== 0) {
            // Si es solo un nombre de archivo, asumir que está en users
            if (strpos($path, '/') === false) {
                $path = self::USERS_DIR . '/' . $path;
            }
        }

        return $path;
    }

    /**
     * Procesa la subida de foto
     */
    public static function processUploadedPhoto(?array $file, int $userId): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Error al subir el archivo (código ' . (int) $file['error'] . ').');
        }

        // Tamaño máximo 5MB
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new \RuntimeException('La imagen excede el tamaño máximo permitido (5MB).');
        }

        // Validar tipo MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $allowedMimes, true)) {
            throw new \RuntimeException('Formato no permitido (solo JPG, PNG o WEBP).');
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        };

        return self::storeProfilePhoto($userId, $file['tmp_name'], $ext);
    }

    /**
     * Guarda una foto en el sistema de archivos
     */
    private static function storeProfilePhoto(int $userId, string $tmpPath, string $ext): string
    {
        $ext = strtolower($ext);
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw new \RuntimeException('Extensión de imagen no soportada.');
        }

        $usersDirAbs = FCPATH . self::USERS_DIR;

        if (!is_dir($usersDirAbs) && !@mkdir($usersDirAbs, 0775, true)) {
            throw new \RuntimeException('No se pudo crear el directorio de usuarios.');
        }

        $ext = ($ext === 'jpeg') ? 'jpg' : $ext;
        $filename = 'user_' . $userId . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $targetAbs = $usersDirAbs . '/' . $filename;

        // Guardar archivo
        if (!@move_uploaded_file($tmpPath, $targetAbs)) {
            if (!@rename($tmpPath, $targetAbs)) {
                throw new \RuntimeException('No se pudo guardar la imagen de perfil.');
            }
        }

        return self::USERS_DIR . '/' . $filename;
    }

    /**
     * Actualiza perfil
     */
    public static function updateUserProfile(int $userId, array $fields, ?array $photoFile = null): bool
    {
        $db = Database::connect();

        // Foto previa
        $current = self::getUserProfile($userId);
        $oldPhotoRel = $current['photo_path'] ?? self::DEFAULT_AVATAR;

        // Procesar nueva foto
        $newPhotoRel = null;
        if ($photoFile) {
            $newPhotoRel = self::processUploadedPhoto($photoFile, $userId);
        }

        // Preparar datos
        $data = [
            'first_name' => trim((string) ($fields['first_name'] ?? '')),
            'last_name' => trim((string) ($fields['last_name'] ?? '')),
            'national_id' => trim((string) ($fields['national_id'] ?? '')),
            'birth_date' => trim((string) ($fields['birth_date'] ?? '')) ?: null,
            'email' => trim((string) ($fields['email'] ?? '')),
            'phone' => trim((string) ($fields['phone'] ?? '')),
        ];

        if ($newPhotoRel !== null) {
            $data['photo_path'] = $newPhotoRel;
        }

        $success = $db->table('users')
            ->where('id', $userId)
            ->update($data);

        // Si se actualizó y hubo nueva foto (distinta del avatar por defecto), borrar la anterior
        if ($success && $newPhotoRel && $oldPhotoRel !== self::DEFAULT_AVATAR) {
            self::deleteOldPhotoIfSafe($oldPhotoRel);
        }

        return (bool) $success;
    }

    /**
     * Elimina foto antigua si es segura
     */
    private static function deleteOldPhotoIfSafe(string $oldRelPath): void
    {
        if (strpos($oldRelPath, self::USERS_DIR . '/') !== 0) {
            return; // solo borramos dentro de assets/img/users
        }

        $abs = FCPATH . $oldRelPath;
        if (is_file($abs)) {
            @unlink($abs);
        }
    }

    /** Cambia la contraseña */
    public static function updatePassword(int $userId, string $currentPlain, string $newPlain): void
    {
        $db = Database::connect();

        $row = $db->table('users')
            ->select('password_hash')
            ->where('id', $userId)
            ->get()
            ->getRowArray();

        if (!$row) {
            throw new \RuntimeException('Usuario no encontrado.');
        }

        $currentHash = (string) $row['password_hash'];
        if (!password_verify($currentPlain, $currentHash)) {
            throw new \RuntimeException('La contraseña actual es incorrecta.');
        }

        $newHash = password_hash($newPlain, PASSWORD_DEFAULT);

        $success = $db->table('users')
            ->where('id', $userId)
            ->update([
                'password_hash' => $newHash,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        if (!$success) {
            throw new \RuntimeException('No se pudo actualizar la contraseña.');
        }
    }

    /**
     * Normaliza fecha para input type="date"
     */
    public static function normalizeDateForInput(?string $date): string
    {
        if (!$date || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return '';
        }

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return '';
        }

        return date('Y-m-d', $timestamp);
    }
}