<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use App\Entity\User;

class UserLoginControllerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function testFormPage()
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPageTitle()
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertSelectorTextContains('h1', 'Connexion');
        $this->assertSelectorTextContains('title', 'Connexion');
    }

    public function testShowForm()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $this->assertSelectorExists('form', "Le formulaire n'existe pas");
        $this->assertCount(1, $crawler->filter('form'));
        $this->assertSelectorExists('form input[name*="email"]', "Le champ du mail n'existe pas");
        $this->assertCount(1, $crawler->filter('input[name*="email"]'), "Il doit y avoir un et un seul champ d'email");
        $this->assertSelectorExists('form input[name*="password"]', "Le champ du mot de passe n'existe pas");
        $this->assertCount(1, $crawler->filter('input[name*="password"]'), "Il doit y avoir un et un seul champ de mot de passe");
        $this->assertSelectorExists('form button[type="submit"]', "Le bouton d'envoi n'existe pas");
        $this->assertCount(1, $crawler->filter('button[type="submit"]'), "Il doit y avoir un et un seul bouton submit");
    }

    public function testIfFormSubmits()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->filter('form')->form();
        $form['user_login[email]'] = 'aaa123@mail.com';
        $form['user_login[password]'] = 'Tssssss0';

        $crawler = $client->submit($form);

        $this->assertEquals(200 || 300, $client->getResponse()->getStatusCode());
    }

    // public function testFormValidity()
    // {
    // }

    public function testSuccessfulConnexion()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->filter('form')->form();
        $form['user_login[email]'] = 'test123@mail.com';
        $form['user_login[password]'] = 'Testp@ss01';

        $crawler = $client->submit($form);
        $this->assertResponseRedirects('/account/logged');
    }


    public function testIfUserExistsInDb()
    {
        $client = static::createClient();

        $this->entityManager = $client->getContainer()
            ->get('doctrine')
            ->getManager();

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['email' => 'tintin.dupont@test.fr'])
        ;
        // var_dump($user->getEmail());
        // die();
        $this->assertSame('tintin.dupont@test.fr', $user->getEmail());
    }

    public function testIfUserDoesNotExistInDb()
    {
        $client = static::createClient();

        $this->entityManager = $client->getContainer()
            ->get('doctrine')
            ->getManager();

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['email' => 'tintin.dupont@test.fr'])
        ;
        $this->assertNotSame('tonton.dupont@test.fr', $user->getEmail());
    }
}
