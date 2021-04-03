<?php

namespace App\Tests\Controller\UserController;

use App\Tests\AuthTestCase;

class UserIndexTest extends AuthTestCase
{
    protected $method = 'GET';

    protected function route(){
        return "/user/";
    }

    public function setUp():void
    {
        parent::setUp();
    }

    public function testUserLoggedAsAdmin()
    {
        $this->logIn('ROLE_ADMIN');
        $crawler = $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testUserLoggedAsAdminCreateNew()
    {
        $this->logIn('ROLE_ADMIN');
        $crawler = $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $link = $crawler
            ->filter('a:contains("Create new")')
            ->eq(0)
            ->link();

        $this->client->click($link);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/user/new", $this->client->getRequest()->getRequestUri());
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
