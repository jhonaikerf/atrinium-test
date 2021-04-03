<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthTestCase extends WebTestCase
{
    protected $client = null;

    protected $method = null;

    protected function route() {
        return null;
    }

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    public function setUp():void
    {
        $this->client = static::createClient();

        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testUserNotLogged()
    {
        $this->client->request($this->method, $this->route());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertEquals('/login', $this->client->getRequest()->getRequestUri());
    }

    protected function logIn($rol)
    {
        $session = self::$container->get('session');

        $user = new User();
        $user->setRoles([$rol]);
        $user->setPassword("\$argon2id\$v=19\$m=65536,t=4,p=1\$fKiMq6nOqA9px/NeRcoeUg\$OkMbTSullX/9mErj0BMHRFJF17fZKKLyvRk/A1huh1A");
        $user->setUsername('username');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    protected function tearDown():void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
