<?php

namespace App\Tests\Controller\UserController;

use App\Entity\User;
use App\Tests\AuthTestCase;

class UserDeleteTest extends AuthTestCase
{
    protected $method = 'GET';

    protected $user;

    protected function route() {
        $id = $this->user->getId();
        return "/user/$id/delete";
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

    public function testUserLoggedAsAdminDelete()
    {
        $this->logIn('ROLE_ADMIN');
        $crawler = $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->client->submitForm('Delete', []);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();
        $this->assertEquals("/user/", $this->client->getRequest()->getRequestUri());

        $oldUsername = $this->user->getUsername();
        $this->entityManager->refresh($this->user);
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['username' => $oldUsername]);
        $this->assertTrue($user == null);
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

    public function testUserNotFound()
    {
        $this->logIn('ROLE_ADMIN');
        $this->user = new User();
        $this->client->request($this->method, $this->route());
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testUserLoggedAsClient()
    {
        $this->logIn('ROLE_CLIENT');
        $this->client->request($this->method, $this->route());

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    protected function tearDown():void
    {
        parent::tearDown();
    }
}
