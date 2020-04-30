<?php

class UpdateFilesTest extends TestCase
{
    /**
     * Test a user can update a file with valid info
     *
     * @return void
     * @test
     */
    public function testUserCanUpdateAFolder()
    {
        $response = $this->asSuperUser('POST', '/v1/files/1/update', [
            'name' => 'Test file (updated)',
            'description' => 'This is a file updated via unit tests.',
        ]);
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertTrue($content['success']);
        $this->assertTrue(array_key_exists('data', $content));
        $this->assertTrue(is_array($content['data']));
        $this->assertTrue(array_key_exists('file', $content['data']));
        $this->assertTrue(is_array($content['data']['file']));
    }

    /**
     * Test validation rules for updating a file
     *
     * @return void
     * @test
     */
    public function testUpdateFolderValidation()
    {
        $response = $this->asSuperUser('POST', '/v1/files/1/update', [
            'name' => '',
            'description' => 'This is a file updated via unit tests.',
        ]);
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(400, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertFalse($content['success']);
        $this->assertTrue(array_key_exists('errors', $content));
        $this->assertTrue(is_array($content['errors']));

        $this->assertTrue(isset($content['errors']['name'][0]));
        $this->assertTrue($content['errors']['name'][0] == 'The name field is required.');
    }
}
