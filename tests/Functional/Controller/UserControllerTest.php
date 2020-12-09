<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class UserControllerTest extends WebTestCase
{
    /*private KernelInterface $kernelIn;

    private function kernelInitialization(): void
    {
        if (is_null($this->kernelIn)) {
            $this->kernelIn = self::bootKernel();
            $this->kernelIn->boot();
        }
    }*/

    private function userInitialization(): User
    {
        $user = new User();
        $user
            ->setCivility("Monsieur")
            ->setFirstName("Dupont")
            ->setLastName("Tintin")
            ->setBillingAddress("1 avenue st martin")
            ->setBillingCity("Colmar")
            ->setBillingPostcode("68000")
            ->setBillingCountry("FR")
            ->setBirthDate(new \DateTime("2000-10-10"))
            ->setPassword("Azerty78")
            ->setEmail("dupond.t@orange.fr")
            ->setTimeZoneSelected("Europe/Paris");
        return $user;
    }

    public function testCreationFormPageValidResponseCode(): void
    {
        $client = static::createClient();
        $client->request('GET', '/account/creation');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testCreationFormPageValidTitle(): void
    {
        $client = static::createClient();
        $client->request('GET', '/account/creation');
        $this->assertSelectorTextContains('h1', 'Créer un compte');
    }

    public function testCreationFormValidDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/account/creation');
        // Balise form
        $this->assertSelectorExists('form[name=user_creation]', "Aucune balise form n'est présente dans ce formulaire");
        $this->assertCount(1, $crawler->filter('form[name=user_creation]'), "Il existe plusieurs balise form dans ce formulaire");
        // Civilité
        $this->assertSelectorExists('form[name=user_creation] *[name*=civility]', "Aucun champs de civilité n'est présent dans ce formulaire");
        $this->assertCount(1, $crawler->filter('form[name=user_creation] *[name*=civility]'), "Il existe plusieurs champs de civilité dans ce formulaire");
        // Nom
        $this->assertSelectorExists('form[name=user_creation] input[name*=lastName]', "Aucun champs de nom n'est présent dans ce formulaire");
        $this->assertCount(1, $crawler->filter('form[name=user_creation] input[name*=lastName]'), "Il existe plusieurs champs de nom dans ce formulaire");
        // Prénom
        $this->assertSelectorExists('form[name=user_creation] input[name*=firstName]', "Aucun champs de prénom n'est présent dans ce formulaire");
        $this->assertCount(1, $crawler->filter('form[name=user_creation] input[name*=firstName]'), "Il existe plusieurs champs de prénom dans ce formulaire");
        // Adresse
        $this->assertSelectorExists('form[name=user_creation] input[name*=Address]', "Aucun champs d'adresse n'est présent dans ce formulaire");
        $this->assertCount(1, $crawler->filter('form[name=user_creation] input[name*=Address]'), "Il existe plusieurs champs d'adresse' dans ce formulaire");
        // Ville
        $this->assertSelectorExists('form[name=user_creation] input[name*=City]', "Aucun champs de ville n'est présent dans ce formulaire");
        $this->assertCount(1, $crawler->filter('form[name=user_creation] input[name*=City]'), "Il existe plusieurs champs de ville dans ce formulaire");
        // Code postal
        $this->assertSelectorExists('form[name=user_creation] input[name*=Postcode]', "Aucun champs de code postal n'est présent dans ce formulaire");
        $this->assertCount(1, $crawler->filter('form[name=user_creation] input[name*=Postcode]'), "Il existe plusieurs champs de code postal dans ce formulaire");
        // Pays
        $this->assertSelectorExists('form[name=user_creation] *[name*=Country]', "Aucun champs de pays n'est présent dans ce formulaire");
        $this->assertCount(1, $crawler->filter('form[name=user_creation] *[name*=Country]'), "Il existe plusieurs champs de pays dans ce formulaire");
        // Date de naissance
        $this->assertSelectorExists('form[name=user_creation] *[name*=birthDate]', "Aucun champs pour la date de naissance n'est présent dans ce formulaire");
        $this->assertCount(1, $crawler->filter('form[name=user_creation] *[name*=birthDate]'), "Il existe plus de 3 champs pour la date de naissance dans ce formulaire");
        // Mot de passe
        $this->assertSelectorExists('form[name=user_creation] input[name*=password]', "Aucun champs de mot de passe n'est présent dans ce formulaire");
        $this->assertCount(2, $crawler->filter('form[name=user_creation] input[name*=password]'), "Il existe plus de 2 champs de mot de passe dans ce formulaire");
        // Email
        $this->assertSelectorExists('form[name=user_creation] input[name*=email]', "Aucun champs email n'est présent dans ce formulaire");
        $this->assertCount(2, $crawler->filter('form[name=user_creation] input[name*=email]'), "Il existe plus de 2 champs email dans ce formulaire");
        // Bouton submit
        $this->assertSelectorExists('form[name=user_creation] *[type=submit]', "Aucun bouton submit n'est présent dans ce formulaire");
        $this->assertCount(1, $crawler->filter('form[name=user_creation] *[type=submit]'), "Il existe plusieurs bouton submit dans ce formulaire");
    }

    public function testCreationFormValidation(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/account/creation');
        $form = $crawler->selectButton('user_creation[save]')->form();
        // set some values
        $form['user_creation[civility]'] = "Monsieur";
        $form['user_creation[lastName]'] = 'Dupond';
        $form['user_creation[firstName]'] = 'Tintin';
        $form['user_creation[billingAddress]'] = '1 avenue st martin';
        $form['user_creation[billingCity]'] = 'Colmar';
        $form['user_creation[billingPostcode]'] = '68000';
        $form['user_creation[billingCountry]'] = 'FR';
        $form['user_creation[birthDate]'] = '20/03/2000';
        $form['user_creation[password][first]'] = 'Lucas678';
        $form['user_creation[password][second]'] = 'Lucas678';
        $form['user_creation[email][first]'] = 'test@test.fr';
        $form['user_creation[email][second]'] = 'test@test.fr';
        // submit the form
        $crawler = $client->submit($form);
        // asserts
        $this->assertResponseRedirects('/main');
    }

    /*public function testDatabasePersist(): void
    {
        //$this->kernelInitialization();
        $newUser = $this->userInitialization();
        $kernel = $this->kernelInitialization();
        $entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $entityManager->persist($newUser);
        $entityManager->flush();
        $savedUser = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'dupond.t@orange.fr']);
        // asserts
        $this->assertEquals($newUser, $savedUser);
    }*/
}
