<?php

class UserLogoutTest extends TestCase
{
    /**
     * Test is a user can successfully register a new account using valid information
     *
     * @return void
     * @test
     */
    public function testUserCanLogout()
    {
        $response = $this->call('POST', '/v1/oauth/logout');

        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertTrue($content['success']);
    }
}
