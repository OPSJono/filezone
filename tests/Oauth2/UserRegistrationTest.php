<?php

class UserRegistrationTest extends BaseOauth
{

    /**
     * Test is a user can successfully register a new account using valid information
     *
     * @return void
     * @test
     */
    public function testRegisterANewUserWithValidInformation()
    {
        $response = $this->asGuest('POST', '/v1/oauth/register', [
            'first_name' => 'Unit',
            'middle_name' => 'Absolute',
            'last_name' => 'Testing',
            'email' => 'unit@test.com',
            'password' => $this->password,
            'password_confirmation' => $this->password,
        ]);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(array_key_exists('success', $content));
        $this->assertTrue($content['success']);
    }

    /**
     * Test is a user can successfully register a new account using valid information
     *
     * @return void
     * @test
     */
    public function testFailureToRegisterANewUserUsingInvalidInformation()
    {
        $response = $this->call('POST', '/v1/oauth/register', [
            'first_name' => '',
            'middle_name' => '',
            'last_name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response2 = $this->call('POST', '/v1/oauth/register', [
            'first_name' => 'Jon',
            'middle_name' => '',
            'last_name' => '',
            'email' => $this->username,
            'password' => 'password',
            'password_confirmation' => 'wrong_password',
        ]);

        $content = json_decode($response->getContent(), true);
        $content2 = json_decode($response2->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertFalse($content['success']);

        // Fields are required
        $this->assertTrue(isset($content['errors']['first_name'][0]));
        $this->assertTrue($content['errors']['first_name'][0] == 'The first name field is required.');

        $this->assertTrue(isset($content['errors']['email'][0]));
        $this->assertTrue($content['errors']['email'][0] == 'The email field is required.');

        $this->assertTrue(isset($content['errors']['password'][0]));
        $this->assertTrue($content['errors']['password'][0] == 'The password field is required.');

        $this->assertTrue(isset($content['errors']['password_confirmation'][0]));
        $this->assertTrue($content['errors']['password_confirmation'][0] == 'The password confirmation field is required.');

        // Fields present pass valdiation checks
        $this->assertTrue(isset($content2['errors']['email'][0]));
        $this->assertTrue($content2['errors']['email'][0] == 'The email has already been taken.');

        $this->assertTrue(isset($content2['errors']['password'][0]));
        $this->assertTrue($content2['errors']['password'][0] == 'The password confirmation does not match.');


    }
}
