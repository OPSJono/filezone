<?php

class UpdateFoldersTest extends TestCase
{
    /**
     * Test a user can update a folder with valid info
     *
     * @return void
     * @test
     */
    public function testUserCanUpdateAFolder()
    {
        $response = $this->asSuperUser('POST', '/v1/folders/1/update', [
            'parent_folder_id' => '',
            'name' => 'Test Folder (updated)',
            'description' => 'This is a folder updated via unit tests.',
        ]);
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertTrue($content['success']);
        $this->assertTrue(array_key_exists('data', $content));
        $this->assertTrue(is_array($content['data']));
        $this->assertTrue(array_key_exists('folder', $content['data']));
        $this->assertTrue(is_array($content['data']['folder']));
    }

    /**
     * Test validation rules for updating a folder
     *
     * @return void
     * @test
     */
    public function testUpdateFolderValidation()
    {
        $response = $this->asSuperUser('POST', '/v1/folders/1/update', [
            'parent_folder_id' => '99999',
            'name' => '',
            'description' => 'This is a folder updated via unit tests.',
        ]);
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(400, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertFalse($content['success']);
        $this->assertTrue(array_key_exists('errors', $content));
        $this->assertTrue(is_array($content['errors']));

        $this->assertTrue(isset($content['errors']['parent_folder_id'][0]));
        $this->assertTrue($content['errors']['parent_folder_id'][0] == 'The selected parent folder id is invalid.');

        $this->assertTrue(isset($content['errors']['name'][0]));
        $this->assertTrue($content['errors']['name'][0] == 'The name field is required.');
    }
}
