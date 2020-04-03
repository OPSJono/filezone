<?php

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;

class FoldersTableSeeder extends Seeder
{
    /**
     * @var $db DatabaseManager
     */
    private DatabaseManager $db;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->db = app()->make('db');
        $this->db->table('folders')->truncate();

        $this->superAdminFolders();
        $this->normalUserFolders();
    }

    private function superAdminFolders()
    {
        $this->db->table('folders')->insert([
            'id' => '1',
            'parent_folder_id' => null,
            'name' => 'SuperAdmin base folder',
            'description' => 'The first folder created for the super user',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->db->table('folders')->insert([
            'id' => '2',
            'parent_folder_id' => 1,
            'name' => 'First subfolder for superadmin',
            'description' => 'The second folder created for the super user',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->db->table('folders')->insert([
            'id' => '3',
            'parent_folder_id' => 1,
            'name' => 'Second subfolder for superadmin',
            'description' => 'The third folder created for the super user',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->db->table('folders')->insert([
            'id' => '4',
            'parent_folder_id' => 2,
            'name' => 'Third level subfolder 1',
            'description' => 'The fourth folder created for the super user',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->db->table('folders')->insert([
            'id' => '5',
            'parent_folder_id' => 3,
            'name' => 'Third level subfolder 2',
            'description' => 'The fifth folder created for the super user',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    private function normalUserFolders()
    {
        $this->db->table('folders')->insert([
            'id' => '6',
            'parent_folder_id' => null,
            'name' => 'User base folder',
            'description' => 'The first folder created for a user',
            'created_by' => 2,
            'updated_by' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->db->table('folders')->insert([
            'id' => '7',
            'parent_folder_id' => 6,
            'name' => 'First subfolder for regular user',
            'description' => 'The second folder created for a user',
            'created_by' => 2,
            'updated_by' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->db->table('folders')->insert([
            'id' => '8',
            'parent_folder_id' => 6,
            'name' => 'Second subfolder for regular user',
            'description' => 'The third folder created for a user',
            'created_by' => 2,
            'updated_by' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->db->table('folders')->insert([
            'id' => '9',
            'parent_folder_id' => 7,
            'name' => 'Third level subfolder 1',
            'description' => 'The fourth folder created for a user',
            'created_by' => 2,
            'updated_by' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->db->table('folders')->insert([
            'id' => '10',
            'parent_folder_id' => 8,
            'name' => 'Third level subfolder 2',
            'description' => 'The fifth folder created for a user',
            'created_by' => 2,
            'updated_by' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
