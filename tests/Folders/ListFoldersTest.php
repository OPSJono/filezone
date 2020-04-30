<?php

class ListFoldersTest extends TestCase
{
    /**
     * Test a user can successfully view their base folders
     *
     * @return void
     * @test
     */
    public function testUserCanListFolders()
    {
        $response = $this->asSuperUser('GET', '/v1/folders');
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertTrue($content['success']);
        $this->assertTrue(array_key_exists('data', $content));
        $this->assertTrue(is_array($content['data']));
        $this->assertTrue(array_key_exists('folders', $content['data']));
        $this->assertTrue(is_array($content['data']['folders']));
    }

    /**
     * Test a user can successfully view a specific folder
     *
     * @return void
     * @test
     */
    public function testUserCanListSpecificFolders()
    {
        $response = $this->asSuperUser('GET', '/v1/folders/1/view');
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertTrue($content['success']);
        $this->assertTrue(array_key_exists('data', $content));
        $this->assertTrue(is_array($content['data']));
        $this->assertTrue(array_key_exists('folders', $content['data']));
        $this->assertTrue(is_array($content['data']['folders']));
    }
}
