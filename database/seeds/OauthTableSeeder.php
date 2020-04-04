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
            'id' => '7904e9fae0b1f4eed9609699d15e6cb51dedd4c9984b119a962a7927b9ef115a682e9db4a7ef42fb',
            'user_id' => 1,
            'client_id' => 2,
            'name' => null,
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $db->table('oauth_refresh_tokens')->insert([
            'id' => '19c90dbd9481b5e94c344523ceb599afed4c1bef896f667e71533a5ab0bc217f22e783278ef20a01',
            'access_token_id' => '7904e9fae0b1f4eed9609699d15e6cb51dedd4c9984b119a962a7927b9ef115a682e9db4a7ef42fb',
            'revoked' => 0,
            'expires_at' => Carbon::now()->addYear(),
        ]);

        $db->table('oauth_access_tokens')->insert([
            'id' => 'ae12126fdb6e764a94c9628a8b00c371c7b344bce68492b73d78c3c334796e41abf6b847ae88f6a2',
            'user_id' => 2,
            'client_id' => 2,
            'name' => null,
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $db->table('oauth_refresh_tokens')->insert([
            'id' => 'dd99031d43338b22e250e074e914d6c3964608bd534d7e50eb772267a4c9ec4666f7499d292b71b9',
            'access_token_id' => 'ae12126fdb6e764a94c9628a8b00c371c7b344bce68492b73d78c3c334796e41abf6b847ae88f6a2',
            'revoked' => 0,
            'expires_at' => Carbon::now()->addYear(),
        ]);

    }
}
