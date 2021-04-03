<?php

namespace App\Tests\Controller\UserController;

use App\Entity\User;
use App\Tests\AuthTestCase;

class UserNewTest extends AuthTestCase
{
    protected $method = 'GET';

    protected function route() {
        return "/user/new";
    }

    public function setUp():void
    {
        parent::setUp();
    }

    public function testUserLoggedAsAdmin()
    {
        $this->logIn('ROLE_ADMIN');
        $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $username = 'new_username_test';
        $this->client->submitForm('Save', [
            'user[username]' => $username,
            'user[roles]' => [],
            'user[sector]' => [],
            'user[password]' => 'new_password'
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['username' => $username]);
        $this->assertTrue($user != null);
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

        $this->client->submitForm('Save', [
            'user[username]' => '',
            'user[roles]' => [],
            'user[sector]' => [],
            'user[password]' => 'pa$$word_new'
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['username' => '']);
        $this->assertTrue($user == null);
    }

    public function testUserLoggedAsAdminEmptyNewPassword()
    {
        $this->logIn('ROLE_ADMIN');
        $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $username = 'username_test';
        $this->client->submitForm('Save', [
            'user[username]' => 'username_test',
            'user[roles]' => [],
            'user[sector]' => [],
            'user[password]' => ''
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['username' => $username]);
        $this->assertTrue($user == null);
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
