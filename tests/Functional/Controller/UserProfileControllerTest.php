<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

/**
 * @covers \UserProfileController
 */
final class UserProfileControllerTest extends WebTestCase
{
    private function getValidUserData(): array
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
            'newPassword1' => "Azerty78",
            'newPassword2' => "Azerty78",
            'oldPassword' => "Azerty78",
            'emailValidationCode' => 123456,
            'newEmail1' => "dupond.m@orange.fr",
            'newEmail2' => "dupond.m@orange.fr",
            'timezone' => "Europe/Paris",
            'residence' => 'filename.pdf',
            'identity' => 'filename.pdf',
            'accurate' => true,
            'newsletters' => false
        ];
    }

    // Tests fonctionnels d'intégrations

    /**
     * @dataProvider profileFormValidPageProvider
     */
    public function testProfileFormValidPage(string $url, string $title): void
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', $title);
    }

    public function profileFormValidPageProvider(): array
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

    public function testProfilePersonalDataFormValidDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Civilité
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] *[name*=civility]'),
            "Il doit y avoir un et un seul champ pour la civilité dans ce formulaire"
        );
        // Nom
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] input[name*=lastName]'),
            "Il doit y avoir un et un seul champ pour le nom dans ce formulaire"
        );
        // Prénom
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] input[name*=firstName]'),
            "Il doit y avoir un et un seul champ pour le prénom dans ce formulaire"
        );
        // Adresse
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] input[name*=Address]'),
            "Il doit y avoir un et un seul champ pour l'adresse dans ce formulaire"
        );
        // Ville
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] input[name*=City]'),
            "Il doit y avoir un et un seul champ pour la ville dans ce formulaire"
        );
        // Code postal
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] input[name*=Postcode]'),
            "Il doit y avoir un et un seul champ pour le code postal dans ce formulaire"
        );
        // Pays
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] *[name*=Country]'),
            "Il doit y avoir un et un seul champ pour le pays dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_personal_data] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function getProfilePersonalDataForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('user_profile_personal_data[modify]')->form();
        $form['user_profile_personal_data[civility]'] = $formData['civility'];
        $form['user_profile_personal_data[lastName]'] = $formData['lastName'];
        $form['user_profile_personal_data[firstName]'] = $formData['firstName'];
        $form['user_profile_personal_data[billingAddress]'] = $formData['address'];
        $form['user_profile_personal_data[billingCity]'] = $formData['city'];
        $form['user_profile_personal_data[billingPostcode]'] = $formData['postcode'];
        $form['user_profile_personal_data[billingCountry]'] = $formData['country'];
        return $form;
    }

    /*public function testProfilePersonalDataFormCivilityLengthOverMax(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $civility = 'azertyuiopqsdfgh';
        $formData['civility'] = $civility;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "La civilité ne peut pas être plus longue que 15 caractères."
        );
    }*/

    /*public function testProfilePersonalDataFormFirstNameEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $firstName = ' ';
        $formData['firstName'] = $firstName;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "Le prénom ne peut pas être vide."
        );
    }*/

    public function testProfilePersonalDataFormFirstNameNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $firstName = 'k2000';
        $formData['firstName'] = $firstName;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "Seules les lettres, les tirets et les apostrophes sont autorisés."
        );
    }

    public function testProfilePersonalDataFormFirstNameLengthUnderMin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $firstName = 'a';
        $formData['firstName'] = $firstName;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "Votre prénom doit avoir au moins 2 caractères."
        );
    }

    public function testProfilePersonalDataFormFirstNameLengthOverMax(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $firstName = 'nomquiestbeaucouptroplongg';
        $formData['firstName'] = $firstName;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "Votre prénom ne doit pas avoir plus de 25 caractères."
        );
    }

    /*public function testProfilePersonalDataFormLastNameEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $lastName = ' ';
        $formData['lastName'] = $lastName;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "Le nom de famille ne peut pas être vide."
        );
    }*/

    public function testProfilePersonalDataFormLastNameNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $lastName = 'k2000';
        $formData['lastName'] = $lastName;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "Seules les lettres, les tirets et les apostrophes sont autorisés."
        );
    }

    public function testProfilePersonalDataFormLastNameLengthUnderMin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $lastName = 'a';
        $formData['lastName'] = $lastName;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "Votre nom de famille doit avoir au moins 2 caractères."
        );
    }

    public function testProfilePersonalDataFormLastNameLengthOverMax(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $lastName = 'nomquiestbeaucouptroplongg';
        $formData['lastName'] = $lastName;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "Votre nom de famille ne doit pas avoir plus de 25 caractères."
        );
    }

    /*public function testProfilePersonalDataFormAddressEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $address = ' ';
        $formData['address'] = $address;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "L'adresse ne peut pas être vide."
        );
    }*/

    public function testProfilePersonalDataFormAddressNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $address = '1 rue G@bin';
        $formData['address'] = $address;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "Les caractères spéciaux ne sont pas autorisés pour l'adresse."
        );
    }

    /*public function testProfilePersonalDataFormCityEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $city = ' ';
        $formData['city'] = $city;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "La ville ne peut pas être vide."
        );
    }*/

    public function testProfilePersonalDataFormCityNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $city = 'St-Martin 1';
        $formData['city'] = $city;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "Les chiffres et les caractères spéciaux ne sont pas autorisés pour la ville."
        );
    }

    /*public function testProfilePersonalDataFormPostcodeEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $postcode = ' ';
        $formData['postcode'] = $postcode;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "Le code postal ne peut pas être vide."
        );
    }*/

    public function testProfilePersonalDataFormPostcodeNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-informations');
        $formData = $this->getValidUserData();
        // set some values
        $postcode = '6800@';
        $formData['postcode'] = $postcode;
        $form = $this->getProfilePersonalDataForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_personal_data]',
            "Les caractères spéciaux ne sont pas autorisés pour le code postal."
        );
    }

    public function testProfileDocumentFormValidDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-documents');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_document]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Justificatif de domicile
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_document] *[name*=residenceProofFile]'),
            "Il doit y avoir un et un seul champ pour le justificatif de domicile dans ce formulaire"
        );
        // Bouton d'ajout du justificatif de domicile
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_document] *[name*=residenceProofAdd]'),
            "Il doit y avoir un et un seul champ pour le bouton d'ajout du justificatif de domicile dans ce formulaire"
        );
        // Justificatif d'identité
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_document] *[name*=identityDocumentFile]'),
            "Il doit y avoir un et un seul champ pour le justificatif d'identité dans ce formulaire"
        );
        // Bouton d'ajout du justificatif d'identité
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_document] *[name*=identityDocumentAdd]'),
            "Il doit y avoir un et un seul champ pour le bouton d'ajout du justificatif d'identité dans ce formulaire"
        );
        // Bouton total de submit
        $this->assertCount(
            2,
            $crawler->filter('form[name=user_profile_document] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    /*public function getProfileDocumentForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('user_profile_document[add]')->form();
        $form['user_profile_document[residenceProof]'] = $formData['residence'];
        $form['user_profile_document[identityDocument]'] = $formData['identity'];
        return $form;
    }*/

    public function testProfileIdentifierFormValidDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/modifier/identifiant');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_identifier]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        /*// Code de validation de l'email
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_identifier] input[name*=emailValidation]'),
            "Il doit y avoir un et un seul champ pour le code reçu par la nouvelle adresse email dans ce formulaire"
        );*/
        // Nouveau email
        $this->assertCount(
            2,
            $crawler->filter(
                'form[name=user_profile_identifier] input[id^=user_profile_identifier_email_][type=email]'
            ),
            "Il doit y avoir 2 et seulement 2 champs pour la nouvelle adresse email dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_identifier] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function getProfileIdentifierForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('user_profile_identifier[modify]')->form();
        //$form['user_profile_identifier[emailValidation]'] = $formData['emailValidationCode'];
        $form['user_profile_identifier[email][first]'] = $formData['newEmail1'];
        $form['user_profile_identifier[email][second]'] = $formData['newEmail2'];
        return $form;
    }

    /*public function testProfileIdentifierFormEmailEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/modifier/identifiant');
        $formData = $this->getValidUserData();
        // set some values
        $email = ' ';
        $formData['newEmail1'] = $email;
        $formData['newEmail2'] = $email;
        $form = $this->getProfileIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_identifier]',
            "L'adresse email ne peut pas être vide."
        );
    }*/

    public function testProfileIdentifierFormEmailNotEqual(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/modifier/identifiant');
        $formData = $this->getValidUserData();
        // set some values
        $email = 'dupond@orange.fr';
        $formData['newEmail2'] = $email;
        $form = $this->getProfileIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_identifier]',
            "Veuillez saisir une nouvelle adresse email valide."
        );
    }

    public function testProfileIdentifierFormEmailNotValid(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/modifier/identifiant');
        $formData = $this->getValidUserData();
        // set some values
        $email = 'test';
        $formData['newEmail1'] = $email;
        $formData['newEmail2'] = $email;
        $form = $this->getProfileIdentifierForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_identifier]',
            "L'adresse email indiqué n'est pas valide."
        );
    }

    public function testProfilePasswordFormValidDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_password]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Nom cacher pour validation
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_password] input[name*=lastName][type=hidden]'),
            "Il doit y avoir un et un seul champ cacher pour le nom dans ce formulaire"
        );
        // Prénom cacher pour validation
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_password] input[name*=firstName][type=hidden]'),
            "Il doit y avoir un et un seul champ cacher pour le prénom dans ce formulaire"
        );
        // Ancien mot de passe
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_password] input[name*=oldPassword][type=password]'),
            "Il doit y avoir un et un seul champ pour l'ancien mot de passe dans ce formulaire"
        );
        // Nouveau mot de passe
        $this->assertCount(
            2,
            $crawler->filter(
                'form[name=user_profile_password] input[id^=user_profile_password_password_][type=password]'
            ),
            "Il doit y avoir 2 et seulement 2 champs pour le nouveau mot de passe dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_password] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function getProfilePasswordForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('user_profile_password[modify]')->form();
        $form['user_profile_password[oldPassword]'] = $formData['oldPassword'];
        $form['user_profile_password[password][first]'] = $formData['newPassword1'];
        $form['user_profile_password[password][second]'] = $formData['newPassword2'];
        return $form;
    }

    /*public function testProfilePasswordFormPasswordWithName(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getValidUserData();
        // set some values
        $password = 'Martin_Dupond';
        $formData['newPassword1'] = $password;
        $formData['newPassword2'] = $password;
        $form = $this->getProfilePasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        dd($client->getResponse()->getContent());
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_password]',
            "Le mot de passe ne doit pas contenir le prénom et/ou le nom."
        );
    }*/

    /*public function testProfilePasswordFormFalseOldPassword(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getValidUserData();
        // set some values
        $passwordOld = 'a9c456';
        $formData['oldPassword'] = $passwordOld;
        $form = $this->getProfilePasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        dd($client->getResponse()->getContent());
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_password]',
            "Votre ancien mot de passe n'est pas le bon."
        );
    }*/

    public function testProfilePasswordFormPasswordUnderMin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getValidUserData();
        // set some values
        $password = 'a2c456';
        $formData['newPassword1'] = $password;
        $formData['newPassword2'] = $password;
        $form = $this->getProfilePasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_password]',
            "Votre mot de passe doit avoir au moins 7 caractères alphanumérique et/ou spéciaux."
        );
    }

    public function testProfilePasswordFormPasswordOnlyNumbers(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getValidUserData();
        // set some values
        $password = '12345678';
        $formData['newPassword1'] = $password;
        $formData['newPassword2'] = $password;
        $form = $this->getProfilePasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_password]',
            "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des chiffres."
        );
    }

    public function testProfilePasswordFormPasswordOnlyLetters(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getValidUserData();
        // set some values
        $password = 'azertyuiop';
        $formData['newPassword1'] = $password;
        $formData['newPassword2'] = $password;
        $form = $this->getProfilePasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_password]',
            "Pour la sécurité de votre mot de passe, vous ne pouvez pas mettre uniquement des lettres."
        );
    }

    public function testProfilePasswordFormPasswordNotEqual(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getValidUserData();
        // set some values
        $password = 'Azerty789';
        $formData['newPassword2'] = $password;
        $form = $this->getProfilePasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_password]',
            "Veuillez saisir un nouveau mot de passe valide."
        );
    }

    public function testProfilePasswordFormPasswordEmpty(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/modifier/mot-de-passe');
        $formData = $this->getValidUserData();
        // set some values
        $password = ' ';
        $formData['newPassword1'] = $password;
        $formData['newPassword2'] = $password;
        $form = $this->getProfilePasswordForm($crawler, $formData);
        // submit the form
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();
        // asserts
        $this->assertSelectorTextContains(
            'form[name=user_profile_password]',
            "Le mot de passe ne peut pas être vide."
        );
    }

    public function testProfileParameterFormValidDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mon-compte/mes-parametres');
        // Balise form
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_parameter]'),
            "Il doit y avoir une et une seule balise form dans ce formulaire"
        );
        // Fuseau horaire
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_parameter] *[name*=timeZoneSelected]'),
            "Il doit y avoir un et un seul champ pour le fuseau horaire dans ce formulaire"
        );
        // Abonnement d'offre et de publicité (Newsletters)
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_parameter] *[name*=acceptNewsletters]'),
            "Il doit y avoir un et un seul champ pour accepter la newsletters dans ce formulaire"
        );
        // Bouton submit
        $this->assertCount(
            1,
            $crawler->filter('form[name=user_profile_parameter] *[type=submit]'),
            "Il doit y avoir un et un seul bouton d'envoi dans ce formulaire"
        );
    }

    public function getProfileParameterForm(Crawler $crawler, array $formData): Form
    {
        $form = $crawler->selectButton('user_profile_parameter[modify]')->form();
        $form['user_profile_parameter[timeZoneSelected]'] = $formData['timezone'];
        $form['user_profile_parameter[acceptNewsletters]'] = $formData['newsletters'];
        return $form;
    }
}
