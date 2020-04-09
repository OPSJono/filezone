<?php

class ViewFileTest extends TestCase
{
    /**
     * Test a user can successfully view info about a file
     *
     * @return void
     * @test
     */
    public function testUserCanListFolders()
    {
        $response = $this->asSuperUser('GET', '/v1/files/1/view');
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertTrue($content['success']);
        $this->assertTrue(array_key_exists('file', $content));
        $this->assertTrue(is_array($content['file']));
    }

    /**
     * Test a user can successfully download a file
     *
     * @return void
     * @test
     */
    public function testUserCanDownloadAFile()
    {
        $response = $this->asSuperUser('GET', '/v1/files/1/download');
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
