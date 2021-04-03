<?php

namespace App\Tests\Controller\CompanyController;

use App\Entity\Company;
use App\Entity\Sector;
use App\Entity\User;
use App\Tests\AuthTestCase;

class CompanyDeleteTest extends AuthTestCase
{
    protected $method = 'GET';

    protected $sector;

    protected $company;

    protected function route() {
        $id = $this->company->getId();
        return "/company/$id/delete";
    }

    public function setUp():void
    {
        parent::setUp();

        $this->sector = new Sector();
        $this->sector->setName('new_sector_test_1');
        $this->entityManager->persist($this->sector);
        $this->entityManager->flush();
        $this->entityManager->refresh($this->sector);

        $this->company = new Company();
        $this->company->setName('new_company_test_1');
        $this->company->setEmail('new_company_test_1@gmail.com');
        $this->company->setSector($this->sector);
        $this->entityManager->persist($this->company);
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
        $this->assertEquals("/company/", $this->client->getRequest()->getRequestUri());

        $oldName = $this->company->getName();
        $this->entityManager->refresh($this->company);
        $company = $this->entityManager->getRepository(Company::class)
            ->findOneBy(['name' => $oldName]);
        $this->assertTrue($company == null);
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
        $this->assertEquals("/company/", $this->client->getRequest()->getRequestUri());

    }

    public function testCompanyNotFound()
    {
        $this->logIn('ROLE_ADMIN');
        $this->company = new Company();
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
