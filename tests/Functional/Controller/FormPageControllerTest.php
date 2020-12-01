<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class DummyFormControllerTest extends WebTestCase
{
    public function testFormPage()
    {
        $client = static::createClient();

        $client->request('GET', '/account/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPageTitle()
    {
        $client = static::createClient();

        $client->request('GET', '/account/create');

        $this->assertSelectorTextContains('h1', 'Créer un compte');
        $this->assertSelectorTextContains('title', 'Créer un compte');
    }

    public function testShowForm()
    {
        $client = static::createClient();

        $client->request('GET', '/account/create');
        $content = $client->getResponse()->getContent();
        $crawler = new Crawler($content);
        $this->assertCount(1, $crawler->filter('form'));
        $this->assertCount(1, $crawler->filter('input[type="text"]'), "Le champ du nom n'est pas implémenté");
        $this->assertCount(1, $crawler->filter('input[type="email"]'), "Le champ de l'email n'est pas implémenté");
        $this->assertCount(1, $crawler->filter('input[type="submit"]'), "Le bouton submit n'est pas implémenté");
    }
}
