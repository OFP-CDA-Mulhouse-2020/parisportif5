<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @covers \UserRegistrationController
 */
final class UserRegistrationControllerTest extends WebTestCase
{
    private function initializeKernel(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }

    private function createValidUser(): User
    {
        $user = new User();
        $user
            ->setFirstName("Martin")
            ->setLastName("Dupond")
            ->setBillingAddress("1 avenue st martin")
            ->setBillingCity("Colmar")
            ->setBillingPostcode("68000")
            ->setBillingCountry("FR")
            ->setBirthDate(new \DateTimeImmutable("2000-10-10"))
            ->setPassword("Azerty78")
            ->setEmail("dupond.m@orange.fr")
            ->setTimeZoneSelected("Europe/Paris");
        return $user;
    }

    private function getValidUserData(): array
    {
        return [
            'firstName' => "Martin",
            'lastName' => "Dupond",
            'address' => "1 avenue st martin",
            'city' => "Colmar",
            'postcode' => "68000",
            'country' => "FR",
            'birthDate' => "2000-10-10",
            'password1' => "Azerty78",
            'password2' => "Azerty78",
            'email1' => "dupond.m@orange.fr",
            'email2' => "dupond.m@orange.fr",
            'timezone' => "Europe/Paris"
        ];
    }

    public function getRegistrationForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('user_registration[save]')->form();
        $form['user_registration[lastName]'] = $formData['lastName'];
        $form['user_registration[firstName]'] = $formData['firstName'];
        $form['user_registration[billingAddress]'] = $formData['address'];
        $form['user_registration[billingCity]'] = $formData['city'];
        $form['user_registration[billingPostcode]'] = $formData['postcode'];
        $form['user_registration[billingCountry]'] = $formData['country'];
        $form['user_registration[birthDate]'] = $formData['birthDate'];
        $form['user_registration[password][first]'] = $formData['password1'];
        $form['user_registration[password][second]'] = $formData['password2'];
        $form['user_registration[email][first]'] = $formData['email1'];
        $form['user_registration[email][second]'] = $formData['email2'];
        return $form;
    }

    // Tests fonctionnels d'intégrations

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
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_registration]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Nom
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_registration] input[name*=lastName]'),
            "Il doit y avoir un et un seul champ pour le nom dans ce formulaire"
        );
        // Prénom
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_registration] input[name*=firstName]'),
            "Il doit y avoir un et un seul champ pour le prénom dans ce formulaire"
        );
        // Adresse
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_registration] input[name*=Address]'),
            "Il doit y avoir un et un seul champ pour l'adresse dans ce formulaire"
        );
        // Ville
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_registration] input[name*=City]'),
            "Il doit y avoir un et un seul champ pour la ville dans ce formulaire"
        );
        // Code postal
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_registration] input[name*=Postcode]'),
            "Il doit y avoir un et un seul champ pour le code postal dans ce formulaire"
        );
        // Pays
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_registration] *[name*=Country]'),
            "Il doit y avoir un et un seul champ pour le pays dans ce formulaire"
        );
        // Date de naissance
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_registration] *[name*=birthDate]'),
            "Il doit y avoir un et un seul champ pour la date de naissance dans ce formulaire"
        );
        // Mot de passe
        $this->assertSelectorExists(
            'form[name=user_registration] input[name*=password]',
            "Aucun champs de mot de passe n'est présent dans ce formulaire"
        );
        $this->assertCount(
            2,
            $crawler->filter('form[name=user_registration] input[name*=password]'),
            "Il existe plus de 2 champs de mot de passe dans ce formulaire"
        );
        // Email
        $this->assertSelectorExists(
            'form[name=user_registration] input[name*=email]',
            "Aucun champs email n'est présent dans ce formulaire"
        );
        $this->assertCount(
            2,
            $crawler->filter('form[name=user_registration] input[name*=email]'),
            "Il existe plus de 2 champs email dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_registration] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function testDatabasePersistence(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        // exist in bdd
        $kernel = $this->initializeKernel();
        $entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $formData['email1']]);
        // asserts
        $this->assertNotNull($user);
    }

    public function testRegistrationFormPasswordUnderMin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $password = 'a2c456';
        $formData['password1'] = $password;
        $formData['password2'] = $password;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Votre mot de passe doit avoir au moins 7 caractères alphanumérique et/ou spéciaux."
        );
    }

    public function testRegistrationFormPasswordOnlyNumbers(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $password = '12345678';
        $formData['password1'] = $password;
        $formData['password2'] = $password;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des chiffres."
        );
    }

    public function testRegistrationFormPasswordOnlyLetters(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $password = 'azertyuiop';
        $formData['password1'] = $password;
        $formData['password2'] = $password;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des lettres."
        );
    }

    public function testRegistrationFormPasswordEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $password = '';
        $formData['password1'] = $password;
        $formData['password2'] = $password;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Le mot de passe ne peut pas être vide."
        );
    }

    public function testRegistrationFormPasswordNotEqual(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $password = 'Azerty789';
        $formData['password2'] = $password;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Veuillez saisir un mot de passe valide."
        );
    }

    public function testRegistrationFormPasswordWithName(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $password = 'Martin_Dupond';
        $formData['password1'] = $password;
        $formData['password2'] = $password;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Le mot de passe ne doit pas contenir le prénom et/ou le nom."
        );
    }

    public function testRegistrationFormEmailEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $email = '';
        $formData['email1'] = $email;
        $formData['email2'] = $email;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "L'adresse email ne peut pas être vide."
        );
    }

    public function testRegistrationFormEmailNotEqual(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $email = 'dupond@orange.fr';
        $formData['email2'] = $email;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Veuillez saisir une adresse email valide."
        );
    }

    public function testRegistrationFormEmailNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $email = 'test';
        $formData['email1'] = $email;
        $formData['email2'] = $email;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "L'adresse email indiqué n'est pas valide."
        );
    }

    public function testRegistrationFormBirthDateUnderMin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $birthDate = '2020-01-01';
        $formData['birthDate'] = $birthDate;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        //dd($client->getResponse()->getContent());
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Vous n'avez pas l'âge requis de 18 ans pour vous inscrire."
        );
    }

    public function testRegistrationFormBirthDateOverMax(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $birthDate = '1800-01-01';
        $formData['birthDate'] = $birthDate;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        //dd($client->getResponse()->getContent());
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Vous dépassez l'âge maximum de 140 ans pour vous inscrire."
        );
    }

    public function testRegistrationFormFirstNameEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $firstName = '';
        $formData['firstName'] = $firstName;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Le prénom ne peut pas être vide."
        );
    }

    public function testRegistrationFormFirstNameNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $firstName = 'k2000';
        $formData['firstName'] = $firstName;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Seules les lettres, les tirets et les apostrophes sont autorisés."
        );
    }

    public function testRegistrationFormFirstNameLengthUnderMin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $firstName = 'a';
        $formData['firstName'] = $firstName;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Votre prénom doit avoir au moins 2 caractères."
        );
    }

    public function testRegistrationFormFirstNameLengthOverMax(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $firstName = 'nomquiestbeaucouptroplongg';
        $formData['firstName'] = $firstName;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Votre prénom ne doit pas avoir plus de 25 caractères."
        );
    }

    public function testRegistrationFormLastNameEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $lastName = '';
        $formData['lastName'] = $lastName;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Le nom de famille ne peut pas être vide."
        );
    }

    public function testRegistrationFormLastNameNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $lastName = 'k2000';
        $formData['lastName'] = $lastName;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Seules les lettres, les tirets et les apostrophes sont autorisés."
        );
    }

    public function testRegistrationFormLastNameLengthUnderMin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $lastName = 'a';
        $formData['lastName'] = $lastName;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Votre nom de famille doit avoir au moins 2 caractères."
        );
    }

    public function testRegistrationFormLastNameLengthOverMax(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $lastName = 'nomquiestbeaucouptroplongg';
        $formData['lastName'] = $lastName;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Votre nom de famille ne doit pas avoir plus de 25 caractères."
        );
    }

    public function testRegistrationFormAddressEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $address = '';
        $formData['address'] = $address;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "L'adresse ne peut pas être vide."
        );
    }

    public function testRegistrationFormAddressNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $address = '1 rue G@bin';
        $formData['address'] = $address;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Les caractères spéciaux ne sont pas autorisés pour l'adresse."
        );
    }

    public function testRegistrationFormCityEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $city = '';
        $formData['city'] = $city;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "La ville ne peut pas être vide."
        );
    }

    public function testRegistrationFormCityNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $city = 'St-Martin 1';
        $formData['city'] = $city;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Les chiffres et les caractères spéciaux ne sont pas autorisés pour la ville."
        );
    }

    public function testRegistrationFormPostcodeEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $postcode = '';
        $formData['postcode'] = $postcode;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Le code postal ne peut pas être vide."
        );
    }

    public function testRegistrationFormPostcodeNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        // set some values
        $postcode = '6800@';
        $formData['postcode'] = $postcode;
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Les caractères spéciaux ne sont pas autorisés pour le code postal."
        );
    }

    // Tests fonctionnels des comportements

    public function testRegistrationFormValidationOk(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        $email = 'nouveau@gmail.com';
        $formData['email1'] = $email;
        $formData['email2'] = $email;
        // set some values
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        // asserts
        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/main');
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains(
            'div.flash-success',
            "Votre compte a été créé ! Son activation sera effective d'ici 24 heures."
        );
    }

    public function testRegistrationFormValidationExistAlready(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $formData = $this->getValidUserData();
        $email = 'tintin.dupont@test.fr';
        $formData['email1'] = $email;
        $formData['email2'] = $email;
        // set some values
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        // asserts
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains(
            'form[name=user_registration]',
            "Inscription impossible avec cette adresse email ! Veuillez en donner une autre pour vous inscrire."
        );
    }
}
