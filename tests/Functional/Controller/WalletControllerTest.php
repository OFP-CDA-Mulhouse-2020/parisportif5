<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Entity\Wallet;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WalletControllerTest extends WebTestCase
{
    // public function testIfPageIsDisplayed(): void
    // {
    //     $client = static::createClient();

    //     $client->request('GET', '/mon-compte/ajouter-des-fonds');

    //     $this->assertEquals(200, $client->getResponse()->getStatusCode());
    // }

    // public function testPageTitle(): void
    // {
    //     $client = static::createClient();

    //     $client->request('GET', '/mon-compte/ajouter-des-fonds');
    //     // var_dump($client->request('GET', '/mon-compte/ajouter-des-fonds'));
    //     // die();
    //     $this->assertSelectorTextContains('h1', 'Ajouter des Fonds');
    //     $this->assertSelectorTextContains('title', 'Ajouter des Fonds');
    // }

    // public function testShowForm(): void
    // {
    //     $client = static::createClient();

    //     $crawler = $client->request('GET', '/mon-compte/ajouter-des-fonds');
    //     $this->assertSelectorExists('form', "Le formulaire n'existe pas");
    //     $this->assertCount(1, $crawler->filter('form'));
    //     $this->assertSelectorExists('form input[name*="amount"]', "Le champ du montant n'existe pas");
    //     $this->assertCount(1, $crawler->filter('input[name*="amount"]'), "Il doit y avoir un et un seul champ de montant");
    // }

    // public function testIfFormSubmits(): void
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/mon-compte/ajouter-des-fonds');

    //     $form = $crawler->filter('form')->form();
    //     $form['add_funds[amount]'] = 10;

    //     $crawler = $client->submit($form);

    //     $this->assertEquals(200 || 300, $client->getResponse()->getStatusCode());
    // }

    // public function testFundsSuccesfullyAdded(): void
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/mon-compte/ajouter-des-fonds');

    //     $form = $crawler->filter('form')->form();
    //     $form['add_funds[amount]'] = 10;

    //     $crawler = $client->submit($form);
    //     $this->assertResponseRedirects('/mon-compte/porte-monnaie');
    // }

    // public function testIfInputIsInvalid(): void
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/mon-compte/ajouter-des-fonds');

    //     $form = $crawler->filter('form')->form();
    //     $form['add_funds[amount]'] = 's';

    //     $crawler = $client->submit($form);
    //     $this->assertSelectorExists('li', "This value is not valid.");
    //     // $this->assertResponseRedirects('/mon-compte/ajouter-des-fonds');
    // }


    // public function testIfWalletCanBeAccessedInDb(): void
    // {
    //     $client = static::createClient();

    //     $this->entityManager = $client->getContainer()
    //         ->get('doctrine')
    //         ->getManager();

    //     $user = $this->entityManager
    //         ->getRepository(User::class)
    //         ->findOneBy(['email' => 'tintin.dupont@test.fr']);
    //     // var_dump($wallet->getAmount());
    //     // die();
    //     $this->assertNotNull($user->getWallet());
    // }

    // public function testIfWalletAmountIsUpdated(): void
    // {
    //     $client = static::createClient();

    //     $this->entityManager = $client->getContainer()
    //         ->get('doctrine')
    //         ->getManager();

    //     $user = $this->entityManager
    //         ->getRepository(User::class)
    //         ->findOneBy(['email' => 'tintin.dupont@test.fr']);
    //     $wallet = $user->getWallet();
    //     $wallet->setAmount(0);
    //     $amount = $wallet->getAmount();
    //     $this->assertEquals(0, $amount);

    //     $amountAdded = 10;
    //     $wallet->setAmount($amount + $amountAdded);
    //     $amount = $wallet->getAmount();
    //     // var_dump($amount);
    //     // die();

    //     $this->assertGreaterThan(0, $amount);
    // }
}
