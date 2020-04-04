<?php

class UserLoginTest extends BaseOauth
{
    /**
     * Test is a user can successfully register a new account using valid information
     *
     * @return void
     * @test
     */
    public function testUserCanLoginWithValidCredentials()
    {
        $this->insertValidPasswordClient();
        $this->insertValidUser();

        $response = $this->loginWithValidUser();

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(array_key_exists('token_type', $content));
        $this->assertTrue(array_key_exists('expires_in', $content));
        $this->assertTrue(array_key_exists('access_token', $content));
        $this->assertTrue(array_key_exists('refresh_token', $content));
    }

    /**
     * Test is a user can successfully register a new account using valid information
     *
     * @return void
     * @test
     */
    public function testUserFailsLoginWithInvalidCredentials()
    {
        $this->insertValidPasswordClient();
        $this->insertValidUser();

        $response = $this->asGuest('POST', '/v1/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'username' => $this->username,
            'password' => 'Wrong Password :)',
            'scope' => '',
        ]);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertTrue(array_key_exists('error', $content));
        $this->assertTrue(array_key_exists('error_description', $content));
        $this->assertTrue(array_key_exists('hint', $content));
        $this->assertTrue(array_key_exists('message', $content));
    }
}
