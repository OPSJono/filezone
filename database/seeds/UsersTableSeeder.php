<?php

use App\Models\User;
use Carbon\Carbon;
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
            'email_verified_at' => Carbon::now(),
        ]);
        User::create([
            'first_name' => 'Agent',
            'middle_name' => null,
            'last_name' => 'Smith',
            'email' => 'Agent@smith.co.uk',
            'superuser' => '0',
            'password' => app()->make('hash')->make('password'),
            'email_verified_at' => Carbon::now(),
        ]);
        factory(User::class, 18)->create();
    }
}
