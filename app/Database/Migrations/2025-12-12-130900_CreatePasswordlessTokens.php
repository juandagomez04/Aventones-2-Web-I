<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePasswordlessTokens extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'token' => [
                'type' => 'CHAR',
                'constraint' => 64,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
            ],
            'used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);          // PK
        $this->forge->addUniqueKey('token');       // UNIQUE token
        $this->forge->addKey('user_id');           // INDEX user_id

        // true = IF NOT EXISTS (no revienta si ya existe)
        $this->forge->createTable('passwordless_tokens', true);
    }

    public function down()
    {
        // true = IF EXISTS
        $this->forge->dropTable('passwordless_tokens', true);
    }
}
