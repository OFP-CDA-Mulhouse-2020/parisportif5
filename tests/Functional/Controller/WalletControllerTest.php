<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WalletControllerTest extends WebTestCase
{
    protected function getTestUser()
    {
        // get or create the user somehow (e.g. creating some users only
        // for tests while loading the test fixtures)
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('tintin.dupont@test.fr');
        return $testUser;
    }

    public function testIfPageIsRedirectWithoutUser(): void
    {
        $client = static::createClient();

        $client->request('GET', '/mon-compte/ajouter-des-fonds');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/connexion');
    }

    public function testIfPageIsDisplayed(): void
    {
        $client = static::createClient();

        $testUser = $this->getTestUser();
        $client->loginUser($testUser);

        $client->request('GET', '/mon-compte/ajouter-des-fonds');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testPageTitle(): void
    {
        $client = static::createClient();

        $testUser = $this->getTestUser();
        $client->loginUser($testUser);

        $client->request('GET', '/mon-compte/ajouter-des-fonds');

        $this->assertSelectorTextContains('h1', 'Ajouter des fonds');
        $this->assertSelectorTextContains('title', 'Ajouter des fonds');
    }

    public function testShowForm(): void
    {
        $client = static::createClient();

        $testUser = $this->getTestUser();
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/mon-compte/ajouter-des-fonds');
        $this->assertSelectorExists('form', "Le formulaire n'existe pas");
        $this->assertCount(1, $crawler->filter('form'));
        $this->assertSelectorExists('form input[name*="amount"]', "Le champ du montant n'existe pas");
        $this->assertCount(1, $crawler->filter('input[name*="amount"]'), "Il doit y avoir un et un seul champ de montant");
    }

    public function testIfFormSubmits(): void
    {
        $client = static::createClient();

        $testUser = $this->getTestUser();
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/mon-compte/ajouter-des-fonds');

        $form = $crawler->filter('form')->form();
        $form['add_funds[amount]'] = 10;

        $crawler = $client->submit($form);

        $this->assertEquals(200 || 300, $client->getResponse()->getStatusCode());
    }

    public function testFundsSuccesfullyAdded(): void
    {
        $client = static::createClient();

        $testUser = $this->getTestUser();
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/mon-compte/ajouter-des-fonds');

        $form = $crawler->filter('form')->form();
        $form['add_funds[amount]'] = 10;

        $crawler = $client->submit($form);
        $this->assertResponseRedirects('/mon-compte/mes-finances');
    }

    public function testIfInputIsInvalid(): void
    {
        $client = static::createClient();

        $testUser = $this->getTestUser();
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/mon-compte/ajouter-des-fonds');

        $form = $crawler->filter('form')->form();
        $form['add_funds[amount]'] = 's';

        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('form[name=add_funds]', "Veuillez saisir le nombre du montant à ajouter au portefeuille.");
    }


    /*public function testIfWalletCanBeAccessedInDb(): void
    {
        $client = static::createClient();

        $this->entityManager = $client->getContainer()
            ->get('doctrine')
            ->getManager();

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'tintin.dupont@test.fr']);

        $this->assertNotNull($user->getWallet());
    }

    public function testIfWalletAmountIsUpdated(): void
    {
        $client = static::createClient();

        $this->entityManager = $client->getContainer()
            ->get('doctrine')
            ->getManager();

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'tintin.dupont@test.fr']);
        $wallet = $user->getWallet();
        $wallet->setAmount(0);
        $amount = $wallet->getAmount();
        $this->assertEquals(0, $amount);

        $amountAdded = 10;
        $wallet->setAmount($amount + $amountAdded);
        $amount = $wallet->getAmount();

        $this->assertGreaterThan(0, $amount);
    }*/
}
