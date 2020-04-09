<?php

use Illuminate\Http\UploadedFile;

class CreateFilesTest extends TestCase
{
    /**
     * Test a user can upload a file with valid info
     *
     * @return void
     * @test
     */
    public function testUserCanUploadAFile()
    {
        $response = $this->asSuperUser('POST', '/v1/files/create', [
            'folder_id' => 2,
            'name' => 'Test File',
            'description' => 'This is a file uploaded via unit tests.',
            'file' => UploadedFile::fake()->image('unittest.png')->size(200)
        ]);
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertTrue($content['success']);
        $this->assertTrue(array_key_exists('file', $content));
        $this->assertTrue(is_array($content['file']));
    }

    /**
     * Test validation rules for uploading a file
     *
     * @return void
     * @test
     */
    public function testUploadFileValidation()
    {
        $response = $this->asSuperUser('POST', '/v1/files/create', [
            'folder_id' => '99999',
            'name' => '',
            'description' => 'This is a folder created  via unit tests.',
        ]);
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(400, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertFalse($content['success']);
        $this->assertTrue(array_key_exists('errors', $content));
        $this->assertTrue(is_array($content['errors']));


        $this->assertTrue(isset($content['errors']['folder_id'][0]));
        $this->assertTrue($content['errors']['folder_id'][0] == 'The selected folder id is invalid.');

        $this->assertTrue(isset($content['errors']['name'][0]));
        $this->assertTrue($content['errors']['name'][0] == 'The name field is required.');
    }
}
