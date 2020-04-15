<?php

class DeleteUsersTest extends TestCase
{
    /**
     * Test a superuser can delete a user
     *
     * @return void
     * @test
     */
    public function testSuperUserCanDeleteAUser()
    {
        $response = $this->asSuperUser('POST', '/v1/users/2/delete');
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(array_key_exists('success', $content));
        $this->assertTrue($content['success']);
    }

    /**
     * Test a regular user can not delete another user
     *
     * @return void
     * @test
     */
    public function testNormalUserCannotDeleteUser()
    {
        $response = $this->asNormalUser('POST', '/v1/users/2/delete');
        $content = json_decode($response->getContent(), true);

//        dd($content);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue(head($content) == 'Unauthorized.');
    }
}
