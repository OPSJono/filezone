<?php

class ListFoldersTest extends TestCase
{
    /**
     * Test is a user can successfully register a new account using valid information
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
        $this->assertTrue(array_key_exists('folders', $content));
        $this->assertTrue(is_array($content['folders']));
    }
}
