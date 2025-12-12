<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class TestDB extends Controller
{
    // ✅ Prueba de conexión
    public function index()
    {
        try {
            $db = Database::connect();

            // Forzar una conexión real con una consulta simple
            $db->query('SELECT 1');

            echo "✅ Conexión OK a la base de datos: " . $db->getDatabase();
        } catch (\Throwable $e) {
            echo "❌ Error de conexión a la base de datos:<br>";
            echo nl2br($e->getMessage());
        }
    }

    // ✅ Contar usuarios en la tabla
    public function usersCount()
    {
        try {
            $db    = Database::connect();
            $query = $db->query('SELECT COUNT(*) AS total FROM users');
            $row   = $query->getRow();

            echo "👥 Total de usuarios en la tabla users: " . ($row->total ?? 0);
        } catch (\Throwable $e) {
            echo "❌ Error al consultar la tabla users:<br>";
            echo nl2br($e->getMessage());
        }
    }

    // ✅ Probar un INSERT directo, sin formulario ni servicios
    public function testInsert()
    {
        try {
            $db      = Database::connect();
            $builder = $db->table('users');

            $ok = $builder->insert([
                'role'          => 'passenger',
                'status'        => 'pending',
                'first_name'    => 'Prueba',
                'last_name'     => 'CI4',
                'national_id'   => 'TEST-' . rand(1000, 9999),
                'birth_date'    => '2000-01-01',
                'email'         => 'test' . rand(1000, 9999) . '@example.com',
                'phone'         => '8888-8888',
                'photo_path'    => null,
                'password_hash' => password_hash('123456', PASSWORD_DEFAULT),
            ]);

            if (!$ok) {
                $error = $db->error();
                echo "❌ Error al insertar en users:<br>";
                echo nl2br($error['message'] ?? 'Error desconocido');
                return;
            }

            echo "✅ Insert realizado correctamente. ID: " . $db->insertID();
        } catch (\Throwable $e) {
            echo "❌ Excepción al insertar en users:<br>";
            echo nl2br($e->getMessage());
        }
    }
}
