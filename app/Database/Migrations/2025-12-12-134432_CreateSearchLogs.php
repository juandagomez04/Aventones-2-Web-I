<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSearchLogs extends Migration
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
                'null' => true,
            ],
            'origin' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
            ],
            'destination' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
            ],
            'results_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('created_at');

        $this->forge->createTable('search_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('search_logs', true);
    }
}
