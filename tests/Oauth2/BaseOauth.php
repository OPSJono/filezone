<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BaseOauth extends TestCase
{

    protected int $client_id = 10000;
    protected string $client_secret = '';
    protected string $token_name = 'Unit Testing Password Token';
    protected string $username = 'unit@test.com';
    protected string $password = 'password';

    protected function insertValidPasswordClient()
    {
        app()->make('db')->table('oauth_clients')->insert([
            'id' => $this->client_id,
            'user_id' => null,
            'name' => $this->token_name,
            'secret' => $this->client_secret,
            'redirect' => '/',
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
        ]);
    }

    protected function insertValidUser()
    {
        return $this->asGuest('POST', '/v1/oauth/register', [
            'id' => 10000,
            'first_name' => 'Unit',
            'middle_name' => 'Absolute',
            'last_name' => 'Testing',
            'email' => $this->username,
            'password' => $this->password,
            'password_confirmation' => $this->password,
        ]);
    }

    protected function loginWithValidUser()
    {
        return $this->asGuest('POST', '/v1/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'username' => $this->username,
            'password' => $this->password,
            'scope' => '',
        ]);
    }
}
