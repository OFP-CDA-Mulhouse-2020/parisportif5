<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use App\Entity\User;

class SecurityControllerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function testFormPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/connexion');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPageTitle(): void
    {
        $client = static::createClient();

        $client->request('GET', '/connexion');

        $this->assertSelectorTextContains('h1', 'Connexion');
        $this->assertSelectorTextContains('title', 'Connexion');
    }

    public function testShowForm(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/connexion');
        $this->assertSelectorExists('form', "Le formulaire n'existe pas");
        $this->assertCount(1, $crawler->filter('form'));
        $this->assertSelectorExists('form input[name*="email"]', "Le champ du mail n'existe pas");
        $this->assertCount(1, $crawler->filter('input[name*="email"]'), "Il doit y avoir un et un seul champ d'email");
        $this->assertSelectorExists('form input[name*="password"]', "Le champ du mot de passe n'existe pas");
        $this->assertCount(1, $crawler->filter('input[name*="password"]'), "Il doit y avoir un et un seul champ de mot de passe");
        $this->assertSelectorExists('form button[type="submit"]', "Le bouton d'envoi n'existe pas");
        $this->assertCount(1, $crawler->filter('button[type="submit"]'), "Il doit y avoir un et un seul bouton submit");
    }

    public function testLoginWithUnknowPassword(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');

        $form = $crawler->filter('form')->form();
        $form['email'] = 'tintin.dupont@test.fr';
        $form['password'] = 'Tssssss0';

        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/connexion');

        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('form[name=login_form]', "Invalid credentials.");
    }

    public function testLoginWithUnknowEmail(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');

        $form = $crawler->filter('form')->form();
        $form['email'] = 'aaa123@mail.com';
        $form['password'] = '@Hadock5';

        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/connexion');

        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('form[name=login_form]', "Email could not be found.");
    }

    public function testSuccessfulLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');

        $form = $crawler->filter('form')->form();
        $form['email'] = 'tintin.dupont@test.fr';
        $form['password'] = '@Hadock5';

        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/');

        $crawler = $client->followRedirect();
        //$this->assertSelectorTextContains('li', 'connexion');
    }

    /*public function testIfUserExistsInDb(): void
    {
        $client = static::createClient();

        $this->entityManager = $client->getContainer()
            ->get('doctrine')
            ->getManager();

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'tintin.dupont@test.fr']);

        $this->assertNotNull($user);
    }

    public function testIfUserDoesNotExistInDb(): void
    {
        $client = static::createClient();

        $this->entityManager = $client->getContainer()
            ->get('doctrine')
            ->getManager();

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'dodo.dupont@test.fr']);

        $this->assertNull($user);
    }*/

    public function testLoginWithUnknowUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');

        $form = $crawler->filter('form')->form();
        $form['email'] = 'tonton.dupont@mail.com';
        $form['password'] = 'Tssssss0';
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/connexion');

        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('form[name=login_form]', "Email could not be found.");
    }
}
