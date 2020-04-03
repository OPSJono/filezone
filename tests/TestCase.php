<?php

use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Laravel\Lumen\Testing\WithoutMiddleware;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    protected $client_id = 10000;
    protected $client_secret = '';
    protected $token_name = 'Unit Testing Password Token';
    protected $username = 'unit@test.com';
    protected $password = 'password';

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Setup the application
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->prepareForTests();
    }

    /**
     * Prepare for the tests
     */
    private function prepareForTests()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

    /**
     * Tear the app down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

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
        return $this->call('POST', '/v1/oauth/register', [
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
        return $this->call('POST', '/v1/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'username' => $this->username,
            'password' => $this->password,
            'scope' => '',
        ]);
    }
}
