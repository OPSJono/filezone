<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()->make('db')->table('users')->truncate();
        User::create([
            'first_name' => 'Jonathan',
            'middle_name' => 'Luke',
            'last_name' => 'Marshall',
            'email' => 'Jonathan@marshalltech.co.uk',
            'superuser' => '1',
            'password' => app()->make('hash')->make('password'),
        ]);
        User::create([
            'first_name' => 'Agent',
            'middle_name' => '',
            'last_name' => 'Smith',
            'email' => 'Agent@smith.co.uk',
            'superuser' => '0',
            'password' => app()->make('hash')->make('password'),
        ]);
        factory(User::class, 18)->create();
    }
}
