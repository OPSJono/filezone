<?php

class UserLoginTest extends BaseOauth
{
    /**
     * Test a user can log in with valid creds
     *
     * @return void
     * @test
     */
    public function testUserCanLoginWithValidCredentials()
    {
        $response = $this->loginWithValidUser();

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(array_key_exists('token_type', $content));
        $this->assertTrue(array_key_exists('expires_in', $content));
        $this->assertTrue(array_key_exists('access_token', $content));
        $this->assertTrue(array_key_exists('refresh_token', $content));
    }

    /**
     * Test the correct error is returned for invalid creds
     *
     * @return void
     * @test
     */
    public function testUserFailsLoginWithInvalidCredentials()
    {
        $response = $this->asGuest('POST', '/v1/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'username' => $this->username,
            'password' => 'Wrong Password :)',
            'scope' => '',
        ]);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(403, $response->getStatusCode());

        $this->assertTrue(array_key_exists('message', $content));
        $this->assertEquals("Invalid Email, Username or Password.", $content['message']);
    }
}
