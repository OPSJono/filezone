<?php

class ListUsersTest extends TestCase
{
    /**
     * Test a user can successfully list other users
     *
     * @return void
     * @test
     */
    public function testSuperUserCanListUsers()
    {
        $response = $this->asSuperUser('GET', '/v1/users');
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertTrue($content['success']);
        $this->assertTrue(array_key_exists('data', $content));
        $this->assertTrue(is_array($content['data']));
        $this->assertTrue(array_key_exists('users', $content['data']));
        $this->assertTrue(is_array($content['data']['users']));
    }

    /**
     * Test a super user can successfully view a specific user
     *
     * @return void
     * @test
     */
    public function testSuperUserCanListSpecificUser()
    {
        $response = $this->asSuperUser('GET', '/v1/users/1/view');
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertTrue($content['success']);
        $this->assertTrue(array_key_exists('data', $content));
        $this->assertTrue(is_array($content['data']));
        $this->assertTrue(array_key_exists('user', $content['data']));
        $this->assertTrue(is_array($content['data']['user']));
    }

    /**
     * Test a regular user can not list users
     *
     * @return void
     * @test
     */
    public function testNormalUserCannotListUsers()
    {
        $response = $this->asNormalUser('GET', '/v1/users');
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue(head($content) == 'Unauthorized.');
    }

    /**
     * Test a regular user can not view another user
     *
     * @return void
     * @test
     */
    public function testNormalUserCannotViewAUser()
    {
        $response = $this->asNormalUser('GET', '/v1/users/1/view');
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue(head($content) == 'Unauthorized.');
    }
}
