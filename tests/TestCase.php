<?php

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    protected User $superUser;
    protected User $normalUser;

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

        $this->superUser = User::find(1);
        $this->normalUser = User::find(2);
    }

    /**
     * Tear the app down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Make a request as a Super User
     *
     * @param string $method
     * @param string $uri
     * @param array $parameters
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param string $content
     * @return Response
     */
    protected function asSuperUser($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        return $this->actingAs($this->superUser, 'api')
            ->call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }

    /**
     * Make a request as a Normal User
     *
     * @param string $method
     * @param string $uri
     * @param array $parameters
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param string $content
     * @return Response
     */
    protected function asNormalUser($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        return $this->actingAs($this->normalUser, 'api')
            ->call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }

    /**
     * Make a request as a Guest
     *
     * @param string $method
     * @param string $uri
     * @param array $parameters
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param string $content
     * @return Response
     */
    protected function asGuest($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        return $this->call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }
}
