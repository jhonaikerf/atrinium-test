<?php

namespace App\Tests\Controller\CompanyController;

use App\Entity\Sector;
use App\Tests\AuthTestCase;

class SectorEditTest extends AuthTestCase
{
    protected $method = 'GET';

    protected $sector;

    protected function route() {
        $id = $this->sector->getId();
        return "/sector/$id/edit";
    }

    public function setUp():void
    {
        parent::setUp();

        $this->sector = new Sector();
        $this->sector->setName('new_sector_test_1');
        $this->entityManager->persist($this->sector);
        $this->entityManager->flush();
    }

    public function testUserLoggedAsAdminCompleted()
    {
        $this->logIn('ROLE_ADMIN');
        $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $newName = 'new_sector_test_1_1';
        $this->client->submitForm('Update', [
            'sector[name]' => $newName,
        ]);

        $formResponse = $this->client->getResponse();
        $this->assertTrue($formResponse->isRedirection());
        $this->client->followRedirect();
        $this->assertEquals("/sector/", $this->client->getRequest()->getRequestUri());

        $this->entityManager->refresh($this->sector);
        $this->assertEquals($newName, $this->sector->getName());
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

        $this->client->submitForm('Update', [
            'sector[name]' => '',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $oldName = $this->sector->getName();
        $this->entityManager->refresh($this->sector);
        $this->assertEquals($oldName, $this->sector->getName());
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
