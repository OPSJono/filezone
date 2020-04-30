<?php

class CreateUsersTest extends TestCase
{
    /**
     * Test a super user can create another user
     *
     * @return void
     * @test
     */
    public function testSuperUserCanCreateAUser()
    {
        $response = $this->asSuperUser('POST', '/v1/users/create', [
            "first_name" => "Test",
            "middle_name" => "",
            "last_name" => "Icicles",
            "email" => "test@unittest.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);
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
     * Test a regular user can not create another user
     *
     * @return void
     * @test
     */
    public function testNormalUserCannotCreateAUser()
    {
        $response = $this->asNormalUser('POST', '/v1/users/create', [
            "first_name" => "Test",
            "middle_name" => "",
            "last_name" => "Icicles",
            "email" => "test@unittest.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue(head($content) == 'Unauthorized.');
    }

    /**
     * Test validation rules for creating a user
     *
     * @return void
     * @test
     */
    public function testCreateUserValidation()
    {
        $response = $this->asSuperUser('POST', '/v1/users/create', [
            "first_name" => "",
            "middle_name" => "d",
            "last_name" => "1",
            "email" => "example.com",
            "password" => "password",
            "password_confirmation" => "gh",
        ]);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertFalse($content['success']);
        $this->assertTrue(array_key_exists('errors', $content));
        $this->assertTrue(is_array($content['errors']));

        $this->assertTrue(isset($content['errors']['first_name'][0]));
        $this->assertTrue($content['errors']['first_name'][0] == 'The first name field is required.');

        $this->assertTrue(isset($content['errors']['middle_name'][0]));
        $this->assertTrue($content['errors']['middle_name'][0] == 'The middle name must be at least 2 characters.');

        $this->assertTrue(isset($content['errors']['last_name'][0]));
        $this->assertTrue($content['errors']['last_name'][0] == 'The last name must be at least 2 characters.');

        $this->assertTrue(isset($content['errors']['email'][0]));
        $this->assertTrue($content['errors']['email'][0] == 'The email must be a valid email address.');

        $this->assertTrue(isset($content['errors']['password'][0]));
        $this->assertTrue($content['errors']['password'][0] == 'The password confirmation does not match.');

    }
}
