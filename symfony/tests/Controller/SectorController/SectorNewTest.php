<?php

namespace App\Tests\Controller\SectorController;

use App\Entity\Sector;
use App\Tests\AuthTestCase;

class SectorNewTest extends AuthTestCase
{
    protected $method = 'GET';

    protected function route() {
        return "/sector/new";
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

        $name = 'new_sector_test_1';
        $this->client->submitForm('Save', [
            'sector[name]' => $name,
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $sector = $this->entityManager->getRepository(Sector::class)
            ->findOneBy(['name' => $name]);
        $this->assertTrue($sector != null);
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
        $this->assertEquals("/sector/", $this->client->getRequest()->getRequestUri());
    }

    public function testUserLoggedAsAdminEmptyNewName()
    {
        $this->logIn('ROLE_ADMIN');
        $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->client->submitForm('Save', [
            'sector[name]' => '',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $sector = $this->entityManager->getRepository(Sector::class)
            ->findOneBy(['name' => '']);
        $this->assertTrue($sector == null);
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
