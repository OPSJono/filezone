<?php

use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Database\Seeder;

class OauthTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * @var $db Connection
         */
        $db = app()->make('db');
        $db->table('oauth_personal_access_clients')->truncate();
        $db->table('oauth_clients')->truncate();
        $db->table('oauth_refresh_tokens')->truncate();
        $db->table('oauth_access_tokens')->truncate();

        $db->table('oauth_clients')->insert([
            'id' => 1,
            'user_id' => null,
            'name' => 'Filezone Personal Access Client',
            'secret' => '8YTcfndScT1vBtBq8OinyB6L8mLnJtf3ignOpwPW',
            'redirect' => 'http://localhost',
            'personal_access_client' => 1,
            'password_client' => 0,
            'revoked' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $db->table('oauth_clients')->insert([
            'id' => 2,
            'user_id' => null,
            'name' => 'Filezone Password Grant Client',
            'secret' => 'k2useT2XqbCWwLOdSg1kngBFdXao7ukrWY05ot20',
            'redirect' => 'http://localhost',
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $db->table('oauth_personal_access_clients')->insert([
            'id' => 1,
            'client_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $db->table('oauth_access_tokens')->insert([
            'id' => '06572b294aea7a0ffd7f008acd26d33ca8b022ae503a1c5f705423cac5a3dc1c602a670477f74e76',
            'user_id' => 1,
            'client_id' => 2,
            'name' => null,
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $db->table('oauth_refresh_tokens')->insert([
            'id' => '6466dd3958b8a6ce90a2720588cc47f6bf44d39afab494fbf9f72e136b9ebf4d58c6848b7762dde4',
            'access_token_id' => '06572b294aea7a0ffd7f008acd26d33ca8b022ae503a1c5f705423cac5a3dc1c602a670477f74e76',
            'revoked' => 0,
            'expires_at' => Carbon::now()->addYear(),
        ]);

        $db->table('oauth_access_tokens')->insert([
            'id' => '16316768ff2569c297f2a98c0223fc3e5c891623e31a23ca20eebfbb043c43d957c94a964b55d63d',
            'user_id' => 2,
            'client_id' => 2,
            'name' => null,
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $db->table('oauth_refresh_tokens')->insert([
            'id' => '8df9ce70dc097aad727bc603d9b481141e6762a003c2cf2135df064d1cdc10cc21dbef5d7b31d13d',
            'access_token_id' => '16316768ff2569c297f2a98c0223fc3e5c891623e31a23ca20eebfbb043c43d957c94a964b55d63d',
            'revoked' => 0,
            'expires_at' => Carbon::now()->addYear(),
        ]);

    }
}
