<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

//use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @covers \UserController
 */
final class UserControllerTest extends WebTestCase
{
    /*private KernelInterface $kernelIn;

    private function kernelInitialization(): void
    {
        if (is_null($this->kernelIn)) {
            $this->kernelIn = self::bootKernel();
            $this->kernelIn->boot();
        }
    }*/

    private function createValidUser(): User
    {
        $user = new User();
        $user
            ->setFirstName("Romain")
            ->setLastName("Balestreri")
            ->setBillingAddress("1 avenue st martin")
            ->setBillingCity("Colmar")
            ->setBillingPostcode("68000")
            ->setBillingCountry("FR")
            ->setBirthDate(new \DateTimeImmutable("2000-10-10"))
            ->setPassword("Azerty78")
            ->setEmail("dupond.t@orange.fr")
            ->setTimeZoneSelected("Europe/Paris");
        return $user;
    }

    public function testRegistrationFormPageValidResponseCode(): void
    {
        $client = static::createClient();
        $client->request('GET', '/inscription');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testRegistrationFormPageValidTitle(): void
    {
        $client = static::createClient();
        $client->request('GET', '/inscription');
        $this->assertSelectorTextContains('h1', 'Créer un compte');
    }

    public function testRegistrationFormValidDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        // Balise form
        $this->assertCount(1, $crawler->filter('form[name=user_registration]'), "Il existe plusieurs ou pas de balise form dans ce formulaire");
        // Nom
        $this->assertCount(1, $crawler->filter('form[name=user_registration] input[name*=lastName]'), "Il existe plusieurs ou pas de champs de nom dans ce formulaire");
        // Prénom
        $this->assertCount(1, $crawler->filter('form[name=user_registration] input[name*=firstName]'), "Il existe plusieurs ou pas de champs de prénom dans ce formulaire");
        // Adresse
        $this->assertCount(1, $crawler->filter('form[name=user_registration] input[name*=Address]'), "Il existe plusieurs ou pas de champs d'adresse' dans ce formulaire");
        // Ville
        $this->assertCount(1, $crawler->filter('form[name=user_registration] input[name*=City]'), "Il existe plusieurs ou pas de champs de ville dans ce formulaire");
        // Code postal
        $this->assertCount(1, $crawler->filter('form[name=user_registration] input[name*=Postcode]'), "Il existe plusieurs ou pas de champs de code postal dans ce formulaire");
        // Pays
        $this->assertCount(1, $crawler->filter('form[name=user_registration] *[name*=Country]'), "Il existe plusieurs ou pas de champs de pays dans ce formulaire");
        // Date de naissance
        $this->assertCount(1, $crawler->filter('form[name=user_registration] *[name*=birthDate]'), "Il existe plusieurs ou pas de champs pour la date de naissance dans ce formulaire");
        // Mot de passe
        $this->assertSelectorExists('form[name=user_registration] input[name*=password]', "Aucun champs de mot de passe n'est présent dans ce formulaire");
        $this->assertCount(2, $crawler->filter('form[name=user_registration] input[name*=password]'), "Il existe plus de 2 champs de mot de passe dans ce formulaire");
        // Email
        $this->assertSelectorExists('form[name=user_registration] input[name*=email]', "Aucun champs email n'est présent dans ce formulaire");
        $this->assertCount(2, $crawler->filter('form[name=user_registration] input[name*=email]'), "Il existe plus de 2 champs email dans ce formulaire");
        // Bouton submit
        $this->assertCount(1, $crawler->filter('form[name=user_registration] *[type=submit]'), "Il existe plusieurs ou pas de bouton d'envoi dans ce formulaire");
    }

    public function testRegistrationFormValidation(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $form = $crawler->selectButton('user_registration[save]')->form();
        // set some values
        //$form['user_creation[civility]'] = "Monsieur";
        $form['user_registration[lastName]'] = 'Dupond';
        $form['user_registration[firstName]'] = 'Tintin';
        $form['user_registration[billingAddress]'] = '1 avenue st martin';
        $form['user_registration[billingCity]'] = 'Colmar';
        $form['user_registration[billingPostcode]'] = '68000';
        $form['user_registration[billingCountry]'] = 'FR';
        $form['user_registration[birthDate]'] = '20/03/2000';
        $form['user_registration[password][first]'] = 'Lucas678';
        $form['user_registration[password][second]'] = 'Lucas678';
        $form['user_registration[email][first]'] = 'test@test.fr';
        $form['user_registration[email][second]'] = 'test@test.fr';
        // submit the form
        $crawler = $client->submit($form);
        // asserts
        $this->assertResponseIsSuccessful();
        //$this->assertResponseRedirects('/main');
        /*$client->followRedirect();
        echo $client->getResponse()->getContent();*/
    }

    /*public function testDatabasePersist(): void
    {
        //$this->kernelInitialization();
        $newUser = $this->createValidUser();
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

        /*$formData = $this->createValidUser();
        $formData->setPassword('1234');
        $view = $this->factory->create(TestedType::class, $formData)
            ->createView();
        $this->assertArrayHasKey('custom_var', $view->vars);
        $this->assertSame('expected value', $view->vars['custom_var']);*/
        /*$client->submitForm('Submit', [
                'comment_form[author]' => 'Fabien',
                'comment_form[text]' => 'Some feedback from an automated functional test',
                'comment_form[email]' => 'me@automat.ed',
                'comment_form[photo]' => dirname(__DIR__, 2).'/public/images/under-construction.gif',
        ]);*/


    public function testRegistrationFormBadPasswordMessage(): void
    {
        $client = static::createClient();
        $user = $this->createValidUser();
        $crawler = $client->request('GET', '/inscription');
        $form = $crawler->selectButton('user_registration[save]')->form();
        // set some values
        $form['user_registration[lastName]'] = $user->getLastName();
        $form['user_registration[firstName]'] = $user->getFirstName();
        $form['user_registration[billingAddress]'] = $user->getBillingAddress();
        $form['user_registration[billingCity]'] = $user->getBillingCity();
        $form['user_registration[billingPostcode]'] = $user->getBillingPostcode();
        $form['user_registration[billingCountry]'] = $user->getBillingCountry();
        $form['user_registration[birthDate]'] = $user->getBirthDate()->format('d/m/Y');
        $form['user_registration[password][first]'] = '1234';
        $form['user_registration[password][second]'] = '1234';
        $form['user_registration[email][first]'] = $user->getEmail();
        $form['user_registration[email][second]'] = $user->getEmail();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains('form[name=user_registration]', "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des chiffres");
        $this->assertSelectorTextContains('form[name=user_registration]', "Votre mot de passe doit avoir plus de 7 caractères");
        //$this->assertSelectorTextContains('form[name=user_registration]', "Mot de passe interdit");
        /*//echo $client->getResponse()->getContent();
        $nodesText = $crawler->filter('form[name=user_registration] li')->extract(['_text']);
        //var_dump($nodesText);
        $this->assertContains("Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des chiffres", $nodesText);
        $this->assertContains("Votre mot de passe doit avoir plus de 7 caractères", $nodesText);*/
    }
}
