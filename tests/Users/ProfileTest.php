<?php

class ProfileTest extends TestCase
{
    /**
     * Test a user can update a folder with valid info
     *
     * @return void
     * @test
     */
    public function testUserCanViewTheirProfile()
    {
        $response = $this->asNormalUser('GET', '/v1/profile');
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
     * Test a user can update a folder with valid info
     *
     * @return void
     * @test
     */
    public function testUserCanUpdateTheirProfile()
    {
        $response = $this->asNormalUser('POST', '/v1/profile/update', [
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
     * Test validation rules for updating a folder
     *
     * @return void
     * @test
     */
    public function testUpdateFolderValidation()
    {
        $response = $this->asSuperUser('POST', '/v1/profile/update', [
            "first_name" => "d",
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
        $this->assertTrue($content['errors']['first_name'][0] == 'The first name must be at least 2 characters.');

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
