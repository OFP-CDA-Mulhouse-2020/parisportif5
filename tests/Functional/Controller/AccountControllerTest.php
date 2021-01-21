<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

/**
 * @covers \AccountController
 */
final class AccountControllerTest extends WebTestCase
{
    /*private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('tintin.dupont@test.fr');

        // simulate $testUser being logged in
        $this->client->loginUser($testUser);
    }*/

    protected function loginTestUser(): KernelBrowser
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('tintin.dupont@test.fr');
        // simulate $testUser being logged in
        $client->loginUser($testUser);
        return $client;
    }

    private function getUpdateUserData(): array
    {
        return [
            'civility' => 'Madame',
            'firstName' => "Martine",
            'lastName' => "Dupont",
            'address' => "2 avenue st martin",
            'city' => "Strasbourg",
            'postcode' => "67000",
            'country' => "FR",
            'birthDate' => "2000-10-01",
            'newPassword1' => "Azerty80",
            'newPassword2' => "Azerty80",
            'oldPassword' => "Azerty78",
            'newEmail1' => "dupont@orange.fr",
            'newEmail2' => "dupont@orange.fr",
            'timezone' => "Europe/Paris",
            'residence' => 'filename.pdf',
            'identity' => 'filename.pdf',
            'accurate' => true,
            'newsletters' => true
        ];
    }

    // Tests fonctionnels d'intégrations

    /**
     * @dataProvider accountFormValidPageProvider
     */
    public function testAccountFormValidPage(string $url, string $title): void
    {
        $client = $this->loginTestUser();
        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', $title);
    }

    public function accountFormValidPageProvider(): array
    {
        /*
            ['/mon-compte/mes-informations', "Données personnelles"],
            ['/mon-compte/mes-newsletters', "Vos abonnements"],
            ['/mon-compte/mes-documents', "Vos documents"],
            ['/mon-compte/mes-options', "Vos options"]
        */
        return [
            ['/mon-compte/mes-informations', "Données personnelles"],
            ['/mon-compte/mes-documents', "Vos documents"],
            ['/mon-compte/mes-parametres', "Vos paramètres"],
            ['/mon-compte/modifier/mot-de-passe', "Modifier le mot de passe du compte"],
            ['/mon-compte/modifier/identifiant', "Modifier l'identifiant du compte"]
        ];
    }

    public function testAccountPersonalDataFormValidDisplay(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_personal_data_form]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Civilité
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_personal_data_form] *[name*=civility]'),
            "Il doit y avoir un et un seul champ pour la civilité dans ce formulaire"
        );
        // Email à titre indicatif
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_personal_data_form] input[name*=email][type=email]'),
            "Il doit y avoir un et un seul champ pour l'email à titre indicatif dans ce formulaire"
        );
        // Mot de passe vide à titre indicatif
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_personal_data_form] input[name*=zeroPassword][type=text]'),
            "Il doit y avoir un et un seul champ pour le mot de passe vide à titre indicatif dans ce formulaire"
        );
        // Nom
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_personal_data_form] input[name*=lastName]'),
            "Il doit y avoir un et un seul champ pour le nom dans ce formulaire"
        );
        // Prénom
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_personal_data_form] input[name*=firstName]'),
            "Il doit y avoir un et un seul champ pour le prénom dans ce formulaire"
        );
        // Adresse
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_personal_data_form] input[name*=Address]'),
            "Il doit y avoir un et un seul champ pour l'adresse dans ce formulaire"
        );
        // Ville
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_personal_data_form] input[name*=City]'),
            "Il doit y avoir un et un seul champ pour la ville dans ce formulaire"
        );
        // Code postal
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_personal_data_form] input[name*=Postcode]'),
            "Il doit y avoir un et un seul champ pour le code postal dans ce formulaire"
        );
        // Pays
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_personal_data_form] *[name*=Country]'),
            "Il doit y avoir un et un seul champ pour le pays dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_personal_data_form] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function getAccountPersonalDataForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('account_personal_data_form[modify]')->form();
        $form['account_personal_data_form[civility]'] = $formData['civility'];
        $form['account_personal_data_form[lastName]'] = $formData['lastName'];
        $form['account_personal_data_form[firstName]'] = $formData['firstName'];
        $form['account_personal_data_form[billingAddress]'] = $formData['address'];
        $form['account_personal_data_form[billingCity]'] = $formData['city'];
        $form['account_personal_data_form[billingPostcode]'] = $formData['postcode'];
        $form['account_personal_data_form[billingCountry]'] = $formData['country'];
        return $form;
    }

    /*public function testAccountPersonalDataFormCivilityLengthOverMax(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $civility = 'azertyuiopqsdfgh';
        $formData['civility'] = $civility;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "La civilité ne peut pas être plus longue que 15 caractères."
        );
    }*/

    /*public function testAccountPersonalDataFormFirstNameEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $firstName = ' ';
        $formData['firstName'] = $firstName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "Le prénom ne peut pas être vide."
        );
    }*/

    public function testAccountPersonalDataFormFirstNameNotValid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $firstName = 'k2000';
        $formData['firstName'] = $firstName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "Seules les lettres, les tirets et les apostrophes sont autorisés."
        );
    }

    public function testAccountPersonalDataFormFirstNameLengthUnderMin(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $firstName = 'a';
        $formData['firstName'] = $firstName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "Votre prénom doit avoir au moins 2 caractères."
        );
    }

    public function testAccountPersonalDataFormFirstNameLengthOverMax(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $firstName = 'nomquiestbeaucouptroplongg';
        $formData['firstName'] = $firstName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "Votre prénom ne doit pas avoir plus de 25 caractères."
        );
    }

    /*public function testAccountPersonalDataFormLastNameEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $lastName = ' ';
        $formData['lastName'] = $lastName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "Le nom de famille ne peut pas être vide."
        );
    }*/

    public function testAccountPersonalDataFormLastNameNotValid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $lastName = 'k2000';
        $formData['lastName'] = $lastName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "Seules les lettres, les tirets et les apostrophes sont autorisés."
        );
    }

    public function testAccountPersonalDataFormLastNameLengthUnderMin(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $lastName = 'a';
        $formData['lastName'] = $lastName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "Votre nom de famille doit avoir au moins 2 caractères."
        );
    }

    public function testAccountPersonalDataFormLastNameLengthOverMax(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $lastName = 'nomquiestbeaucouptroplongg';
        $formData['lastName'] = $lastName;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "Votre nom de famille ne doit pas avoir plus de 25 caractères."
        );
    }

    /*public function testAccountPersonalDataFormAddressEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $address = ' ';
        $formData['address'] = $address;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "L'adresse ne peut pas être vide."
        );
    }*/

    public function testAccountPersonalDataFormAddressNotValid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $address = '1 rue G@bin';
        $formData['address'] = $address;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "Les caractères spéciaux ne sont pas autorisés pour l'adresse."
        );
    }

    /*public function testAccountPersonalDataFormCityEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $city = ' ';
        $formData['city'] = $city;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "La ville ne peut pas être vide."
        );
    }*/

    public function testAccountPersonalDataFormCityNotValid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $city = 'St-Martin 1';
        $formData['city'] = $city;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "Les chiffres et les caractères spéciaux ne sont pas autorisés pour la ville."
        );
    }

    /*public function testAccountPersonalDataFormPostcodeEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $postcode = ' ';
        $formData['postcode'] = $postcode;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "Le code postal ne peut pas être vide."
        );
    }*/

    public function testAccountPersonalDataFormPostcodeNotValid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getUpdateUserData();
        // set some values
        $postcode = '6800@';
        $formData['postcode'] = $postcode;
        $form = $this->getAccountPersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_personal_data_form]',
            "Les caractères spéciaux ne sont pas autorisés pour le code postal."
        );
    }

    public function testAccountDocumentFormValidDisplay(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-documents');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_document_form]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Justificatif de domicile
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_document_form] *[name*=residenceProofFile]'),
            "Il doit y avoir un et un seul champ pour le justificatif de domicile dans ce formulaire"
        );
        // Bouton d'ajout du justificatif de domicile
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_document_form] *[name*=residenceProofReplace]'),
            "Il doit y avoir un et un seul champ pour le bouton d'ajout du justificatif de domicile dans ce formulaire"
        );
        // Justificatif d'identité
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_document_form] *[name*=identityDocumentFile]'),
            "Il doit y avoir un et un seul champ pour le justificatif d'identité dans ce formulaire"
        );
        // Bouton d'ajout du justificatif d'identité
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_document_form] *[name*=identityDocumentReplace]'),
            "Il doit y avoir un et un seul champ pour le bouton d'ajout du justificatif d'identité dans ce formulaire"
        );
        // Bouton total de submit
        $this->assertCount(
            2,
            $crawler->filter('form[name=account_document_form] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    /*public function getAccountDocumentForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('account_document_form[add]')->form();
        $form['account_document_form[residenceProof]'] = $formData['residence'];
        $form['account_document_form[identityDocument]'] = $formData['identity'];
        return $form;
    }*/

    public function testAccountIdentifierFormValidDisplay(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/modifier/identifiant');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_update_identifier_form]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        /*// Code de validation de l'email
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_update_identifier_form] input[name*=emailValidation]'),
            "Il doit y avoir un et un seul champ pour le code reçu par la nouvelle adresse email dans ce formulaire"
        );*/
        // Nouveau email
        $this->assertCount(
            2,
            $crawler->filter(
                'form[name=account_update_identifier_form] input[id^=account_update_identifier_form_email_][type=email]'
            ),
            "Il doit y avoir 2 et seulement 2 champs pour la nouvelle adresse email dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_update_identifier_form] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function getAccountIdentifierForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('account_update_identifier_form[modify]')->form();
        //$form['account_update_identifier_form[emailValidation]'] = $formData['emailValidationCode'];
        $form['account_update_identifier_form[email][first]'] = $formData['newEmail1'];
        $form['account_update_identifier_form[email][second]'] = $formData['newEmail2'];
        return $form;
    }

    /*public function testAccountIdentifierFormEmailEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/modifier/identifiant');
        $formData = $this->getUpdateUserData();
        // set some values
        $email = ' ';
        $formData['newEmail1'] = $email;
        $formData['newEmail2'] = $email;
        $form = $this->getAccountIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_update_identifier_form]',
            "L'adresse email ne peut pas être vide."
        );
    }*/

    public function testAccountIdentifierFormEmailNotEqual(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/modifier/identifiant');
        $formData = $this->getUpdateUserData();
        // set some values
        $email = 'dupond@orange.fr';
        $formData['newEmail2'] = $email;
        $form = $this->getAccountIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_update_identifier_form]',
            "Veuillez saisir une nouvelle adresse email valide."
        );
    }

    public function testAccountIdentifierFormEmailNotValid(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/modifier/identifiant');
        $formData = $this->getUpdateUserData();
        // set some values
        $email = 'test';
        $formData['newEmail1'] = $email;
        $formData['newEmail2'] = $email;
        $form = $this->getAccountIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_update_identifier_form]',
            "L'adresse email indiqué n'est pas valide."
        );
    }

    public function testAccountPasswordFormValidDisplay(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_update_password_form]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Nom cacher pour validation
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_update_password_form] input[name*=lastName][type=hidden]'),
            "Il doit y avoir un et un seul champ cacher pour le nom dans ce formulaire"
        );
        // Prénom cacher pour validation
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_update_password_form] input[name*=firstName][type=hidden]'),
            "Il doit y avoir un et un seul champ cacher pour le prénom dans ce formulaire"
        );
        // Ancien mot de passe
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_update_password_form] input[name*=oldPassword][type=password]'),
            "Il doit y avoir un et un seul champ pour l'ancien mot de passe dans ce formulaire"
        );
        // Nouveau mot de passe
        $this->assertCount(
            2,
            $crawler->filter(
                'form[name=account_update_password_form] input[id^=account_update_password_form_password_][type=password]'
            ),
            "Il doit y avoir 2 et seulement 2 champs pour le nouveau mot de passe dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=account_update_password_form] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function getAccountPasswordForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('account_update_password_form[modify]')->form();
        $form['account_update_password_form[oldPassword]'] = $formData['oldPassword'];
        $form['account_update_password_form[plainPassword][first]'] = $formData['newPassword1'];
        $form['account_update_password_form[plainPassword][second]'] = $formData['newPassword2'];
        return $form;
    }

    /*public function testAccountPasswordFormPasswordWithName(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getUpdateUserData();
        // set some values
        $password = 'Martin_Dupond';
        $formData['newPassword1'] = $password;
        $formData['newPassword2'] = $password;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        dd($client->getResponse()->getContent());
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_update_password_form]',
            "Le mot de passe ne doit pas contenir le prénom et/ou le nom."
        );
    }*/

    /*public function testAccountPasswordFormFalseOldPassword(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getUpdateUserData();
        // set some values
        $passwordOld = 'a9c456';
        $formData['oldPassword'] = $passwordOld;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        dd($client->getResponse()->getContent());
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_update_password_form]',
            "Votre ancien mot de passe n'est pas le bon."
        );
    }*/

    public function testAccountPasswordFormPasswordUnderMin(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getUpdateUserData();
        // set some values
        $password = 'a2c456';
        $formData['newPassword1'] = $password;
        $formData['newPassword2'] = $password;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_update_password_form]',
            "Votre mot de passe doit avoir au moins 7 caractères alphanumérique et/ou spéciaux."
        );
    }

    public function testAccountPasswordFormPasswordOnlyNumbers(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getUpdateUserData();
        // set some values
        $password = '12345678';
        $formData['newPassword1'] = $password;
        $formData['newPassword2'] = $password;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_update_password_form]',
            "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des chiffres."
        );
    }

    public function testAccountPasswordFormPasswordOnlyLetters(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getUpdateUserData();
        // set some values
        $password = 'azertyuiop';
        $formData['newPassword1'] = $password;
        $formData['newPassword2'] = $password;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_update_password_form]',
            "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des lettres."
        );
    }

    public function testAccountPasswordFormPasswordNotEqual(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getUpdateUserData();
        // set some values
        $password = 'Azerty789';
        $formData['newPassword2'] = $password;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_update_password_form]',
            "Veuillez saisir un nouveau mot de passe valide."
        );
    }

    public function testAccountPasswordFormPasswordEmpty(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getUpdateUserData();
        // set some values
        $password = ' ';
        $formData['newPassword1'] = $password;
        $formData['newPassword2'] = $password;
        $form = $this->getAccountPasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=account_update_password_form]',
            "Le mot de passe ne peut pas être vide."
        );
    }

    public function testAccountParameterFormValidDisplay(): void
    {
        $client = $this->loginTestUser();
        $crawler = $client->request('GET', '/mon-compte/mes-parametres');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_update_parameter]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Fuseau horaire
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_update_parameter] *[name*=timeZoneSelected]'),
            "Il doit y avoir un et un seul champ pour le fuseau horaire dans ce formulaire"
        );
        // Abonnement d'offre et de publicité (Newsletters)
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_update_parameter] *[name*=acceptNewsletters]'),
            "Il doit y avoir un et un seul champ pour accepter la newsletters dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_update_parameter] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function getAccountParameterForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('user_update_parameter[modify]')->form();
        $form['user_update_parameter[timeZoneSelected]'] = $formData['timezone'];
        $form['user_update_parameter[acceptNewsletters]'] = $formData['newsletters'];
        return $form;
    }
}
