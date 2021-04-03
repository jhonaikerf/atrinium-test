<?php

namespace App\Tests\Controller\CompanyController;

use App\Entity\Company;
use App\Entity\Sector;
use App\Tests\AuthTestCase;

class CompanyNewTest extends AuthTestCase
{
    protected $method = 'GET';

    protected $sector;

    protected function route() {
        return "/company/new";
    }

    public function setUp():void
    {
        parent::setUp();

        $this->sector = new Sector();
        $this->sector->setName('new_sector_test_1');
        $this->entityManager->persist($this->sector);
        $this->entityManager->flush();
    }

    public function testUserLoggedAsAdmin()
    {
        $this->logIn('ROLE_ADMIN');
        $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $name = 'new_company_test_1';
        $this->client->submitForm('Save', [
            'company[name]' => $name,
            'company[sector]' => $this->sector->getId()
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $company = $this->entityManager->getRepository(Company::class)
            ->findOneBy(['name' => $name]);
        $this->assertTrue($company != null);
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

    public function testUserLoggedAsAdminEmptyNewName()
    {
        $this->logIn('ROLE_ADMIN');
        $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->client->submitForm('Save', [
            'company[name]' => '',
            'company[sector]' => $this->sector->getId()
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $company = $this->entityManager->getRepository(Company::class)
            ->findOneBy(['name' => '']);
        $this->assertTrue($company == null);
    }

    public function testUserLoggedAsAdminExistingEmail()
    {
        $this->logIn('ROLE_ADMIN');
        $this->client->request($this->method, $this->route());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $oldCompany = new Company();
        $oldCompany->setName('new_company_test_1');
        $oldCompany->setEmail('new_company_test_1@gmail.com');
        $oldCompany->setSector($this->sector);
        $this->entityManager->persist($oldCompany);
        $this->entityManager->flush();

        $name = 'new_company_test_2';
        $this->client->submitForm('Save', [
            'company[name]' => $name,
            'company[email]' => $oldCompany->getEmail(),
            'company[sector]' => $this->sector->getId()
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $company = $this->entityManager->getRepository(Company::class)
            ->findOneBy(['name' => $name]);
        $this->assertTrue($company == null);
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
