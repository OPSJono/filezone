<?php

class DeleteFoldersTest extends TestCase
{
    /**
     * Test a user can update a folder with valid info
     *
     * @return void
     * @test
     */
    public function testUserCanDeleteAFolder()
    {
        $response = $this->asSuperUser('POST', '/v1/folders/1/delete');
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertTrue($content['success']);
    }
}
