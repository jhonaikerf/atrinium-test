<?php

namespace App\Tests\Controller\CompanyController;

use App\Tests\AuthTestCase;

class SectorIndexTest extends AuthTestCase
{
    protected $method = 'GET';

    protected function route(){
        return "/sector/";
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
        $this->assertEquals("/sector/new", $this->client->getRequest()->getRequestUri());
    }

    public function testUserLoggedAsClient()
    {
        $this->logIn('ROLE_CLIENT');
        $crawler = $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(0, $crawler->filter('a:contains("Create new")')->count());
    }

    protected function tearDown():void
    {
        parent::tearDown();
    }
}
