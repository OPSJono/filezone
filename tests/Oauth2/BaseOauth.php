<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BaseOauth extends TestCase
{

    protected int $client_id = 2;
    protected string $client_secret = 'k2useT2XqbCWwLOdSg1kngBFdXao7ukrWY05ot20';
    protected $token_name = null;
    /**
     * @var string $username case sensitive
     */
    protected string $username = 'Jonathan@marshalltech.co.uk';
    protected string $password = 'password';

    protected function loginWithValidUser()
    {
//        $clients = \Illuminate\Support\Facades\DB::table('oauth_clients')->get();
//        dd($clients);

        return $this->asGuest('POST', '/v1/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'username' => $this->username,
            'password' => $this->password,
            'scope' => '*',
//            'redirect_uri' => ''
        ]);
    }
}
