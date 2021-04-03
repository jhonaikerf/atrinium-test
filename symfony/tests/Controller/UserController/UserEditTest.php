<?php

namespace App\Tests\Controller\UserController;

use App\Entity\User;
use App\Tests\AuthTestCase;

class UserEditTest extends AuthTestCase
{
    protected $method = 'GET';

    protected $user;

    protected function route() {
        $id = $this->user->getId();
        return "/user/$id/edit";
    }

    public function setUp():void
    {
        parent::setUp();

        $this->user = new User();
        $this->user->setRoles([]);
        $this->user->setPassword("\$argon2id\$v=19\$m=65536,t=4,p=1\$fKiMq6nOqA9px/NeRcoeUg\$OkMbTSullX/9mErj0BMHRFJF17fZKKLyvRk/A1huh1A");
        $this->user->setUsername('username2');
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
    }

    public function testUserLoggedAsAdmin()
    {
        $this->logIn('ROLE_ADMIN');
        $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $newUsername = 'new_username_edit';
        $oldPassword = $this->user->getPassword();
        $this->client->submitForm('Update', [
            'user[username]' => $newUsername,
            'user[roles]' => [],
            'user[sector]' => [],
            'user[password]' => ''
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();
        $this->assertEquals("/user/", $this->client->getRequest()->getRequestUri());

        $this->entityManager->refresh($this->user);
        $this->assertEquals($newUsername, $this->user->getUsername());
        $this->assertEquals($oldPassword, $this->user->getPassword());
    }

    public function testUserLoggedAsAdminCancel()
    {
        $this->logIn('ROLE_ADMIN');
        $crawler = $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $link = $crawler
            ->filter('a:contains("Cancel")')
            ->eq(0)
            ->link();

        $this->client->click($link);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/user/", $this->client->getRequest()->getRequestUri());
    }

    public function testUserLoggedAsAdminEmptyNewUsername()
    {
        $this->logIn('ROLE_ADMIN');
        $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->client->submitForm('Update', [
            'user[username]' => '',
            'user[roles]' => [],
            'user[sector]' => [],
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $oldUsername = $this->user->getUsername();
        $this->entityManager->refresh($this->user);
        $this->assertEquals($oldUsername, $this->user->getUsername());
    }

    public function testUserLoggedAsAdminNewPassword()
    {
        $this->logIn('ROLE_ADMIN');
        $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->client->submitForm('Update', [
            'user[username]' => $this->user->getUsername(),
            'user[roles]' => [],
            'user[sector]' => [],
            'user[password]' => 'pa$$word_new'
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $oldUsername = $this->user->getUsername();
        $oldPassword = $this->user->getPassword();
        $this->entityManager->refresh($this->user);
        $this->assertEquals($oldUsername, $this->user->getUsername());
        $this->assertNotEquals($oldPassword, $this->user->getPassword());
    }

    public function testUserNotFound()
    {
        $this->logIn('ROLE_ADMIN');
        $this->user = new User();
        $crawler = $this->client->request($this->method, $this->route());
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testUserLoggedAsClient()
    {
        $this->logIn('ROLE_CLIENT');
        $crawler = $this->client->request($this->method, $this->route());

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    protected function tearDown():void
    {
        parent::tearDown();
    }
}
