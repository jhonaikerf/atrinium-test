<?php

namespace App\Tests\Controller\SectorController;

use App\Entity\Sector;
use App\Tests\AuthTestCase;

class SectorDeleteTest extends AuthTestCase
{
    protected $method = 'GET';

    protected $sector;

    protected function route() {
        $id = $this->sector->getId();
        return "/sector/$id/delete";
    }

    public function setUp():void
    {
        parent::setUp();

        $this->sector = new Sector();
        $this->sector->setName('new_sector_test_1');
        $this->entityManager->persist($this->sector);
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
        $this->assertEquals("/sector/", $this->client->getRequest()->getRequestUri());

        $oldName = $this->sector->getName();
        $this->entityManager->refresh($this->sector);
        $sector = $this->entityManager->getRepository(Sector::class)
            ->findOneBy(['name' => $oldName]);
        $this->assertTrue($sector == null);
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

    public function testSectorNotFound()
    {
        $this->logIn('ROLE_ADMIN');
        $this->sector = new Sector();
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
