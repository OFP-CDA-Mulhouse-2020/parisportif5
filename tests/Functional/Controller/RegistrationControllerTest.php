<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Component\DomCrawler\Form;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \RegistrationController
 */
final class RegistrationControllerTest extends WebTestCase
{
    /*protected function initializeKernel(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }*/

    protected function getValidUserData(): array
    {
        return [
            'civility' => '',
            'firstName' => "Martin",
            'lastName' => "Dupond",
            'address' => "1 avenue st martin",
            'city' => "Colmar",
            'postcode' => "68000",
            'country' => "FR",
            'birthDate' => "2000-10-10",
            'plainPassword1' => "Azerty78",
            'plainPassword2' => "Azerty78",
            'email1' => "dupond.m@orange.fr",
            'email2' => "dupond.m@orange.fr",
            'timezone' => "Europe/Paris",
            'residence' => __DIR__ . '/../../../docs/Wireframe/WireframeParisSportifs.pdf',
            'identity' => __DIR__ . '/../../../docs/Wireframe/WireframeParisSportifs.pdf',
            'accurate' => true,
            'newsletters' => false,
            'acceptTerms' => true
        ];
    }

    protected function getRegistrationForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->filter('form *[name*=registerNewUser][type=submit]')->form();
        $formName = $form->getName();
        $form->disableValidation();
        $form[$formName . '[newsletters]'] = $formData['newsletters'];
        $form[$formName . '[acceptTerms]'] = $formData['acceptTerms'];
        $form[$formName . '[certifiesAccurate]'] = $formData['accurate'];
        $form[$formName . '[residenceProof]'] = $formData['residence'];
        $form[$formName . '[identityDocument]'] = $formData['identity'];
        $form[$formName . '[timeZoneSelected]'] = $formData['timezone'];
        $form[$formName . '[lastName]'] = $formData['lastName'];
        $form[$formName . '[firstName]'] = $formData['firstName'];
        $form[$formName . '[billingAddress]'] = $formData['address'];
        $form[$formName . '[billingCity]'] = $formData['city'];
        $form[$formName . '[billingPostcode]'] = $formData['postcode'];
        $form[$formName . '[billingCountry]'] = $formData['country'];
        $form[$formName . '[birthDate]'] = $formData['birthDate'];
        $form[$formName . '[newPassword][first]'] = $formData['plainPassword1'];
        $form[$formName . '[newPassword][second]'] = $formData['plainPassword2'];
        $form[$formName . '[email][first]'] = $formData['email1'];
        $form[$formName . '[email][second]'] = $formData['email2'];
        return $form;
    }

    protected function getRegisterPageUrl(): string
    {
        return '/inscription';
    }

    // Tests fonctionnels d'intégrations

    public function testRegistrationFormValidResponseCode(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->getRegisterPageUrl());
        $this->assertResponseStatusCodeSame(200);
        //dump($client->getResponse()->getContent());
    }

    public function testRegistrationFormValidTitle(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->getRegisterPageUrl());
        $this->assertSelectorTextContains('h1', 'Inscription');
    }

    public function testRegistrationFormValidDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formName = $crawler->filter('form *[name*=registerNewUser][type=submit]')->form()->getName();
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . ']'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Nom
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=lastName]'),
            "Il doit y avoir un et un seul champ pour le nom dans ce formulaire"
        );
        // Prénom
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=firstName]'),
            "Il doit y avoir un et un seul champ pour le prénom dans ce formulaire"
        );
        // Adresse
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=Address]'),
            "Il doit y avoir un et un seul champ pour l'adresse dans ce formulaire"
        );
        // Ville
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=City]'),
            "Il doit y avoir un et un seul champ pour la ville dans ce formulaire"
        );
        // Code postal
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] input[name*=Postcode]'),
            "Il doit y avoir un et un seul champ pour le code postal dans ce formulaire"
        );
        // Pays
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=Country]'),
            "Il doit y avoir un et un seul champ pour le pays dans ce formulaire"
        );
        // Date de naissance
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=birthDate]'),
            "Il doit y avoir un et un seul champ pour la date de naissance dans ce formulaire"
        );
        // Fuseau horaire
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=timeZoneSelected]'),
            "Il doit y avoir un et un seul champ pour le fuseau horaire dans ce formulaire"
        );
        // Mot de passe
        $this->assertCount(
            2,
            $crawler->filter('form[name=' . $formName . '] input[name*=newPassword][type=password]'),
            "Il doit y avoir 2 et seulement 2 champs pour le mot de passe dans ce formulaire"
        );
        // Email
        $this->assertCount(
            2,
            $crawler->filter('form[name=' . $formName . '] input[name*=email][type=email]'),
            "Il doit y avoir 2 et seulement 2 champs pour l'email dans ce formulaire"
        );
        // Accepter les conditions générales
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=acceptTerms][type=checkbox]'),
            "Il doit y avoir un et un seul champ pour accepter les conditions générales dans ce formulaire"
        );
        // Newsletters (abonnements)
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=newsletters][type=checkbox]'),
            "Il doit y avoir un et un seul champ pour accepter les newsletters dans ce formulaire"
        );
        // Justificatif de domicile
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=residenceProof]'),
            "Il doit y avoir un et un seul champ pour le justificatif de domicile dans ce formulaire"
        );
        // Justificatif d'identité
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=identityDocument]'),
            "Il doit y avoir un et un seul champ pour le justificatif d'identité dans ce formulaire"
        );
        // Certifie l'exactitude des informations
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[name*=certifiesAccurate][type=checkbox]'),
            "Il doit y avoir un et un seul champ pour certifier l'exactitude des informations dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=' . $formName . '] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    /*public function testDatabasePersistence(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        $email = 'nouveau@gmail.com';
        $formData['email1'] = $email;
        $formData['email2'] = $email;
        // set some values
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        // exist in bdd
        //$kernel = $this->initializeKernel();
        //$entityManager = $kernel->getContainer()
        $entityManager = $client->getContainer()
            ->get('doctrine')
            ->getManager();
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $formData['email1']]);
        // asserts
        $this->assertNotNull($user);
    }*/

    public function testRegistrationFormPasswordUnderMin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $password = 'a2c456';
        $formData['plainPassword1'] = $password;
        $formData['plainPassword2'] = $password;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Votre mot de passe doit avoir au moins 7 caractères alphanumérique et/ou spéciaux."
        );
    }

    public function testRegistrationFormPasswordOnlyNumbers(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $password = '12345678';
        $formData['plainPassword1'] = $password;
        $formData['plainPassword2'] = $password;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des chiffres."
        );
    }

    public function testRegistrationFormPasswordOnlyLetters(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $password = 'azertyuiop';
        $formData['plainPassword1'] = $password;
        $formData['plainPassword2'] = $password;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des lettres."
        );
    }

    public function testRegistrationFormPasswordEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $password = ' ';
        $formData['plainPassword1'] = $password;
        $formData['plainPassword2'] = $password;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le mot de passe ne peut pas être vide."
        );
    }

    public function testRegistrationFormPasswordNotEqual(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $password = 'Azerty789';
        $formData['plainPassword2'] = $password;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Veuillez saisir un mot de passe valide."
        );
    }

    public function testRegistrationFormPasswordWithName(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $password = 'Martin_Dupond';
        $formData['plainPassword1'] = $password;
        $formData['plainPassword2'] = $password;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le mot de passe ne doit pas contenir le prénom et/ou le nom."
        );
    }

    public function testRegistrationFormEmailEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $email = '';
        $formData['email1'] = $email;
        $formData['email2'] = $email;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "L'adresse email ne peut pas être vide."
        );
    }

    public function testRegistrationFormEmailNotEqual(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $email = 'dupond@orange.fr';
        $formData['email2'] = $email;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Veuillez saisir une adresse email valide."
        );
    }

    public function testRegistrationFormEmailNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $email = 'test';
        $formData['email1'] = $email;
        $formData['email2'] = $email;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "L'adresse email indiqué n'est pas valide."
        );
    }

    public function testRegistrationFormBirthDateUnderMin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $birthDate = '2020-01-01';
        $formData['birthDate'] = $birthDate;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        //dd($client->getResponse()->getContent());
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Vous n'avez pas l'âge requis de 18 ans pour vous inscrire."
        );
    }

    public function testRegistrationFormBirthDateOverMax(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $birthDate = '1800-01-01';
        $formData['birthDate'] = $birthDate;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        //dd($client->getResponse()->getContent());
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Vous dépassez l'âge maximum de 140 ans pour vous inscrire."
        );
    }

    public function testRegistrationFormFirstNameEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $firstName = '';
        $formData['firstName'] = $firstName;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le prénom ne peut pas être vide."
        );
    }

    public function testRegistrationFormFirstNameNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $firstName = 'k2000';
        $formData['firstName'] = $firstName;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Seules les lettres, les tirets et les apostrophes sont autorisés."
        );
    }

    public function testRegistrationFormFirstNameLengthUnderMin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $firstName = 'a';
        $formData['firstName'] = $firstName;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Votre prénom doit avoir au moins 2 caractères."
        );
    }

    public function testRegistrationFormFirstNameLengthOverMax(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $firstName = 'nomquiestbeaucouptroplongg';
        $formData['firstName'] = $firstName;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Votre prénom ne doit pas avoir plus de 25 caractères."
        );
    }

    public function testRegistrationFormLastNameEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $lastName = '';
        $formData['lastName'] = $lastName;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le nom de famille ne peut pas être vide."
        );
    }

    public function testRegistrationFormLastNameNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $lastName = 'k2000';
        $formData['lastName'] = $lastName;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Seules les lettres, les tirets et les apostrophes sont autorisés."
        );
    }

    public function testRegistrationFormLastNameLengthUnderMin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $lastName = 'a';
        $formData['lastName'] = $lastName;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Votre nom de famille doit avoir au moins 2 caractères."
        );
    }

    public function testRegistrationFormLastNameLengthOverMax(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $lastName = 'nomquiestbeaucouptroplongg';
        $formData['lastName'] = $lastName;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Votre nom de famille ne doit pas avoir plus de 25 caractères."
        );
    }

    public function testRegistrationFormAddressEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $address = '';
        $formData['address'] = $address;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "L'adresse ne peut pas être vide."
        );
    }

    public function testRegistrationFormAddressNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $address = '1 rue G@bin';
        $formData['address'] = $address;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Les caractères spéciaux ne sont pas autorisés pour l'adresse."
        );
    }

    public function testRegistrationFormCityEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $city = '';
        $formData['city'] = $city;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "La ville ne peut pas être vide."
        );
    }

    public function testRegistrationFormCityNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $city = 'St-Martin 1';
        $formData['city'] = $city;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Les chiffres et les caractères spéciaux ne sont pas autorisés pour la ville."
        );
    }

    public function testRegistrationFormPostcodeEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $postcode = '';
        $formData['postcode'] = $postcode;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le code postal ne peut pas être vide."
        );
    }

    public function testRegistrationFormPostcodeNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $postcode = '6800@';
        $formData['postcode'] = $postcode;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Les caractères spéciaux ne sont pas autorisés pour le code postal."
        );
    }

    public function testRegistrationFormAcceptTermsNotCheck(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $acceptTerms = false;
        $formData['acceptTerms'] = $acceptTerms;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Vous devez accepter les conditions générales d'utilisation pour vous inscrire."
        );
    }

    public function testRegistrationFormCertifiesAccurateNotCheck(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $certifiesAccurate = false;
        $formData['accurate'] = $certifiesAccurate;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Vous devez certifier sur l'honneur que les données fournies sont exactes."
        );
    }

    public function testRegistrationFormIdentityDocumentEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $identityDocument = '';
        $formData['identity'] = $identityDocument;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Fichier obligatoire !"
        );
    }

    public function testRegistrationFormIdentityDocumentSizeNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $identityDocument = __DIR__ . '/../../../docs/Wireframe/WireframeParisSportifs.zip';
        $formData['identity'] = $identityDocument;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le fichier est trop volumineux. La taille maximale autorisée est de 1 Mio."
        );
    }

    public function testRegistrationFormIdentityDocumentFormatNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $identityDocument = __DIR__ . '/../../../docs/Wireframe/WireframeParisSportifs.bmpr';
        $formData['identity'] = $identityDocument;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Seule les fichiers au format PDF, PNG, JPG et JPEG sont accepté."
        );
    }

    public function testRegistrationFormResidenceProofEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $residenceProof = '';
        $formData['residence'] = $residenceProof;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Fichier obligatoire !"
        );
    }

    public function testRegistrationFormResidenceProofSizeNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $residenceProof = __DIR__ . '/../../../docs/Wireframe/WireframeParisSportifs.zip';
        $formData['residence'] = $residenceProof;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le fichier est trop volumineux. La taille maximale autorisée est de 1 Mio."
        );
    }

    public function testRegistrationFormResidenceProofFormatNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $residenceProof = __DIR__ . '/../../../docs/Wireframe/WireframeParisSportifs.bmpr';
        $formData['residence'] = $residenceProof;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Seule les fichiers au format PDF, PNG, JPG et JPEG sont accepté."
        );
    }

    public function testRegistrationFormTimezoneEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $timezone = '';
        $formData['timezone'] = $timezone;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Le fuseau horaire sélectionné ne peut pas être vide."
        );
    }

    public function testRegistrationFormTimezoneNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        // set some values
        $timezone = "Paris";
        $formData['timezone'] = $timezone;
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Veuillez sélectionner un fuseau horaire valide."
        );
    }

    // Tests fonctionnels des comportements

    public function testRegistrationFormValidationOk(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        //$email = 'nouveau@gmail.com';
        //$formData['email1'] = $email;
        //$formData['email2'] = $email;
        // set some values
        $form = $this->getRegistrationForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        // asserts
        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/');
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains(
            'div.flash-success',
            "Votre compte a été créé ! Son activation sera effective d'ici 24 heures."
        );
    }

    public function testRegistrationFormValidationExistAlready(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRegisterPageUrl());
        $formData = $this->getValidUserData();
        $email = 'tintin.dupont@test.fr';
        $formData['email1'] = $email;
        $formData['email2'] = $email;
        // set some values
        $form = $this->getRegistrationForm($crawler, $formData);
        $formName = $form->getName();
        // submit the form
        $crawler = $client->submit($form);
        // asserts
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains(
            'form[name=' . $formName . ']',
            "Inscription impossible avec cette adresse email ! Veuillez en donner une autre pour vous inscrire."
        );
    }
}
