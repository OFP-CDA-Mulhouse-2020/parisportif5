<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class DummyFormControllerTest extends WebTestCase
{
    public function testFormPage()
    {
        $client = static::createClient();

        $client->request('GET', '/account/connect');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPageTitle()
    {
        $client = static::createClient();

        $client->request('GET', '/account/connect');

        $this->assertSelectorTextContains('h1', 'Connexion');
        $this->assertSelectorTextContains('title', 'Connexion');
    }

    public function testShowForm()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/account/connect');
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
        $crawler = $client->request('GET', '/account/connect');

        $form = $crawler->filter('form')->form();
        $form['user_login[emailAddress]'] = 'aaa@test.com';
        $form['user_login[password]'] = 'Testpass01';

        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    // public function testFormValidity()
    // {
    //     $client = static::createClient([
    //         'environment' => 'test'
    //     ]);
    //     $userRepository = static::$container->get(UserRepository::class);

    //     $testUser = $userRepository->findOneByEmailAddress('monsieurdupont3@adresse.com');

    //     $client->loginUser($testUser);
        
    // }
}
