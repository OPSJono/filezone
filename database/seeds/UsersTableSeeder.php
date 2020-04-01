<?php

use App\User;
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
            'password' => app()->make('hash')->make('password'),
        ]);
        factory(User::class, 19)->create();
    }
}
